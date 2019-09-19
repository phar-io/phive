<?php
namespace PharIo\Phive;

use DateTimeImmutable;
use Gnupg;
use PharIo\FileSystem\Directory;

class PublicKeyReader {

    /** @var Gnupg */
    private $gnupg;

    /** @var Directory */
    private $workDir;

    /**
     * PublicKeyReader constructor.
     *
     * @param Gnupg $gnupg
     */
    public function __construct(Gnupg $gnupg, Directory $workDir) {
        $this->gnupg = $gnupg;
        $this->workDir = $workDir;
    }

    public function parse(string $id, string $content): PublicKey {
        $this->gnupg->import($content);
        $result = $this->gnupg->keyinfo($id)[0];

        $uids = [];
        foreach($result['uids'] as $pos => $uid) {
            $uids[] = $uid['uid'];
        }

        if (empty($uids)) {
            throw new PublicKeyException('No UIDs in key found');
        }

        $fingerprint = $result['subkeys'][0]['fingerprint'] ?? '';
        $timestamp   = $result['subkeys'][0]['timestamp'] ?? 0;

        if ($fingerprint === '') {
            throw new PublicKeyException('No fingerprint in key found');
        }

        $this->cleanUp();

        return new PublicKey(
            $id,
            $fingerprint,
            $uids,
            $content,
            new DateTimeImmutable('@' . $timestamp)
        );
    }

    private function cleanUp() {
        $this->workDir->file('pubring.kbx')->delete();
        $this->workDir->file('pubring.kbx~')->delete();
        $this->workDir->file('trustdb.gpg')->delete();
    }
}
