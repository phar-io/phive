<?php declare(strict_types = 1);
namespace PharIo\Phive;

class PublicKey {

    /** @var string */
    private $id;

    /** @var string */
    private $fingerprint;

    /** @var array */
    private $uids = [];

    /** @var string */
    private $public;

    /** @var \DateTimeImmutable */
    private $created;

    /** @var string */
    private $bits;

    /**
     * @throws PublicKeyException
     */
    public function __construct(string $id, string $info, string $public) {
        $this->id     = $id;
        $this->public = $public;
        $this->parseInfo($info);
    }

    public function getId(): string {
        return $this->id;
    }

    public function getInfo(): string {
        $info   = [];
        $info[] = "\tFingerprint: " . $this->fingerprint;
        $info[] = '';

        foreach ($this->uids as $uid => $time) {
            /* @var $time \DateTimeImmutable */
            $info[] = \sprintf("\t%s (%s)", $uid, $time->format('Y-m-d'));
        }
        $info[] = '';
        $info[] = "\tCreated: " . $this->created->format('Y-m-d');

        return \implode("\n", $info);
    }

    public function getKeyData(): string {
        return $this->public;
    }

    public function getFingerprint(): string {
        return \str_replace(' ', '', $this->fingerprint);
    }

    public function getBits(): string {
        return $this->bits;
    }

    /**
     * @throws PublicKeyException
     */
    private function parseInfo($info): void {
        foreach (\explode("\n", $info) as $line) {
            $parts = \explode(':', \urldecode($line));

            switch ($parts[0]) {
                default: {
                    continue 2;
                }
                case 'uid': {
                    // 0   1                                      2
                    // uid:Sebastian Bergmann <sebastian@php.net>:1405755775::
                    $this->uids[$parts[1]] = new \DateTimeImmutable('@' . $parts[2]);

                    break;
                }
                case 'pub': {
                    // 0   1                                        2 3    4
                    // pub:D8406D0D82947747293778314AA394086372C20A:1:4096:1405754086::
                    $this->fingerprint = \trim(\chunk_split($parts[1], 4, ' '));
                    $this->bits        = $parts[3];
                    $this->created     = new \DateTimeImmutable('@' . $parts[4]);

                    break;
                }
            }
        }

        if (empty($this->uids) || $this->fingerprint === null || $this->bits === null || $this->created === null) {
            throw new PublicKeyException(
                \sprintf('Failed to parse provided key info: %s', $info)
            );
        }
    }
}
