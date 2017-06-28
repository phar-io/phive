<?php
namespace PharIo\Phive;

use ArrayIterator;
use Iterator;
use PharIo\Manifest\PhpExtensionRequirement;
use PharIo\Version\VersionConstraint;

class CompatibilityResult {

    /**
     * @var VersionConstraint
     */
    private $phpVersion = NULL;

    /**
     * @var PhpExtensionRequirement[]
     */
    private $extensions = [];

    public function requiresPHPVersion(VersionConstraint $phpVersion) {
        $this->phpVersion = $phpVersion;
    }

    /**
     * @param PhpExtensionRequirement $extension
     */
    public function missesExtension(PhpExtensionRequirement $extension) {
        $this->extensions[] = $extension;
    }

    public function isCompatible() {
        return ($this->phpVersion === NULL) && (count($this->extensions) === 0);
    }

    public function asString() {
        $msg = '';

        if ($this->phpVersion !== NULL) {
            $msg = sprintf(
                "PHP Version constraint %s (running: %s)\n\n",
                $this->phpVersion->asString(),
                PHP_VERSION
            );
        }

        if (count($this->extensions) > 0) {
            $msg .= "Missing extension(s):\n";
            foreach($this->extensions as $extension) {
                $msg .= sprintf("- %s\n", $extension);
            }

            $msg .= "\n\n";
        }

        return $msg;
    }

}
