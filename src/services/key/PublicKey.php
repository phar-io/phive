<?php declare(strict_types = 1);
namespace PharIo\Phive;

class PublicKey {

    /** @var string */
    private $id;

    /** @var string */
    private $fingerprint;

    /** @var array */
    private $uids;

    /** @var string */
    private $public;

    /** @var \DateTimeImmutable */
    private $created;

    /**
     * PublicKey constructor.
     */
    public function __construct(string $id, string $fingerprint, array $uids, string $public, \DateTimeImmutable $created) {
        $this->id          = $id;
        $this->fingerprint = $fingerprint;
        $this->uids        = $uids;
        $this->public      = $public;
        $this->created     = $created;
    }

    public function getId(): string {
        return $this->id;
    }

    public function getInfo(): string {
        $info   = [];
        $info[] = "\tFingerprint: " . \implode(' ', \str_split($this->fingerprint, 4));
        $info[] = '';

        foreach ($this->uids as $uid) {
            $info[] = \sprintf("\t%s", $uid);
        }
        $info[] = '';
        $info[] = "\tCreated: " . $this->created->format('Y-m-d');

        return \implode("\n", $info);
    }

    public function getKeyData(): string {
        return $this->public;
    }

    public function getFingerprint(): string {
        return $this->fingerprint;
    }
}
