<?php
namespace PharIo\Phive;

use PharIo\Manifest\PhpExtensionRequirement;
use PharIo\Manifest\PhpVersionRequirement;
use PharIo\Version\Version;

class CompatibilityChecker {

    /**
     * @param Phar $phar
     *
     * @return CompatibilityResult
     */
    public function checkCompatibility(Phar $phar) {

        $manifest = $phar->getManifest();
        $result = new CompatibilityResult();
        $result->missesExtension(new PhpExtensionRequirement('dummy-for-test-only'));

        foreach($manifest->getRequirements() as $requirement) {

            switch (true) {
                case $requirement instanceof PhpVersionRequirement: {
                    $php = $requirement->getVersionConstraint();
                    if (!$php->complies(new Version(PHP_VERSION))) {
                        $result->requiresPHPVersion($php);
                    }
                    continue 2;
                }

                case $requirement instanceof PhpExtensionRequirement: {
                    if (!extension_loaded( (string)$requirement)) {
                        $result->missesExtension($requirement);
                    }
                }
            }
        }

        return $result;
    }
}
