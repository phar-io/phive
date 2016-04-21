<?php
namespace PharIo\Phive;

class PublicKey {

    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $fingerprint;

    /**
     * @var array
     */
    private $uids = [];

    /**
     * @var string
     */
    private $public;

    /**
     * @var \DateTimeImmutable
     */
    private $created;

    /**
     * @var string
     */
    private $bits;

    /**
     * PublicKey constructor.
     *
     * @param string $id
     * @param string $public
     * @param string $info
     */
    public function __construct($id, $info, $public) {
        $this->id = $id;
        $this->public = $public;
        $this->parseInfo($info);
    }

    /**
     * @return string
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getInfo() {
        $info = [];
        $info[] = "\tFingerprint: " . $this->fingerprint;
        $info[] = '';
        foreach ($this->uids as $uid => $time) {
            /** @var $time \DateTimeImmutable */
            $info[] = sprintf("\t%s (%s)", $uid, $time->format('Y-m-d'));
        }
        $info[] = '';
        $info[] = "\tCreated: " . $this->created->format('Y-m-d');
        return join("\n", $info);
    }

    /**
     * @return string
     */
    public function getKeyData() {
        return $this->public;
    }

    /**
     * @return string
     */
    public function getFingerprint() {
        return str_replace(' ', '', $this->fingerprint);
    }

    private function parseInfo($info) {
        foreach (explode("\n", $info) as $line) {
            $parts = explode(':', $line);
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
                    $this->fingerprint = trim(chunk_split($parts[1], 4, ' '));
                    $this->bits = $parts[3];
                    $this->created = new \DateTimeImmutable('@' . $parts[4]);
                    break;
                }
            }
        }
    }

}
