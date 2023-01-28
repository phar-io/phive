<?php declare(strict_types=1);
/*
 * This file is part of Phive.
 *
 * Copyright (c) Arne Blankerts <arne@blankerts.de>, Sebastian Heuer <sebastian@phpeople.de> and contributors
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */
namespace PharIo\Phive;

use DateTimeImmutable;
use PharIo\FileSystem\Directory;
use PharIo\FileSystem\Filename;

class PublicKeyReader {
    /** @var GnuPG */
    private $gnupg;

    /** @var Directory */
    private $workDir;

    /**
     * PublicKeyReader constructor.
     */
    public function __construct(GnuPG $gnupg, Directory $workDir) {
        $this->gnupg   = $gnupg;
        $this->workDir = $workDir;
    }

    public function parse(string $id, string $content): PublicKey {
        $this->gnupg->import($content);
        $result = $this->gnupg->keyinfo($id)[0];

        $uids = [];

        foreach ($result['uids'] as $pos => $uid) {
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

    private function cleanUp(): void {
        $list = [
            $this->workDir->file('trustdb.gpg'),

            // GnuPG 1.x only
            $this->workDir->file('pubring.gpg'),
            $this->workDir->file('pubring.gpg~'),
            $this->workDir->file('secring.gpg'),

            // GnuPG 2.x only
            $this->workDir->file('pubring.kbx'),
            $this->workDir->file('pubring.kbx~')
        ];

        foreach ($list as $file) {
            /** @var Filename $file */
            if ($file->exists()) {
                $file->delete();
            }
        }
    }
}
