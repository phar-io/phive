<?php
namespace PharIo\Phive;

class EnvironmentLocator {

    /**
     * @var Factory
     */
    private $factory;

    /**
     * @param Factory $factory
     */
    public function __construct(Factory $factory) {
        $this->factory = $factory;
    }

    /**
     * @param string $operatingSystem
     *
     * @return Environment
     */
    public function getEnvironment($operatingSystem) {
        if (strtoupper(substr($operatingSystem, 0, 3)) === 'WIN') {
            return WindowsEnvironment::fromSuperGlobals();
        }
        return UnixoidEnvironment::fromSuperGlobals();
    }

}