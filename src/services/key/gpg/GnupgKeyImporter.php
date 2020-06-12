<?php declare(strict_types = 1);
namespace PharIo\Phive;

class GnupgKeyImporter implements KeyImporter {
    /** @var GnuPG */
    private $gnupg;

    public function __construct(GnuPG $gnupg) {
        $this->gnupg = $gnupg;
    }

    public function importKey(string $key): KeyImportResult {
        $result = $this->gnupg->import($key);

        return new KeyImportResult(
            $result['imported'] ?? 0,
            $result['fingerprint'] ?? ''
        );
    }
}
