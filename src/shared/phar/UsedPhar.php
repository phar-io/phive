<?php declare(strict_types = 1);
namespace PharIo\Phive;

use PharIo\FileSystem\File;
use PharIo\Version\Version;

class UsedPhar extends Phar {
    /** @var string[] */
    private $usages;

    public function __construct(
        string $name,
        Version $version,
        File $file,
        array $usages,
        string $signatureFingerprint = null
    ) {
        parent::__construct($name, $version, $file, $signatureFingerprint);
        $this->usages = $usages;
    }

    /**
     * @return string[]
     */
    public function getUsages(): array {
        return $this->usages;
    }
}
