<?php
namespace PharIo\Phive;

class GnupgKeyImporter implements KeyImporter {

    /**
     * @var \Gnupg
     */
    private $gnupg;

    /**
     * @param \Gnupg $gnupg
     */
    public function __construct(\Gnupg $gnupg) {
        $this->gnupg = $gnupg;
    }

    /**
     * @param string $key
     *
     * @return KeyImportResult|void
     */
    public function importKey($key) {
        $result = $this->gnupg->import($key);

        return new KeyImportResult(
            $result['imported'],
            isset($result['fingerprint']) ? $result['fingerprint'] : ''
        );
    }

}
