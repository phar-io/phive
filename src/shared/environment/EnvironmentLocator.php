<?php declare(strict_types = 1);
namespace PharIo\Phive;

class EnvironmentLocator {
    public function getEnvironment(string $operatingSystem): Environment {
        if (\strtoupper(\substr($operatingSystem, 0, 3)) === 'WIN') {
            return WindowsEnvironment::fromSuperGlobals();
        }

        return UnixoidEnvironment::fromSuperGlobals();
    }
}
