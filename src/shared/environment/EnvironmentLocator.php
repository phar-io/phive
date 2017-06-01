<?php
namespace PharIo\Phive;

class EnvironmentLocator {

    /**
     * @param string $operatingSystem
     *
     * @return Environment
     */
    public function getEnvironment($operatingSystem) {
        if (strtolower($operatingSystem) === 'darwin') {
            return MacOsEnvironment::fromSuperGlobals();
        }
        if (strtoupper(substr($operatingSystem, 0, 3)) === 'WIN') {
            return WindowsEnvironment::fromSuperGlobals();
        }
        return UnixoidEnvironment::fromSuperGlobals();
    }

}
