<?php
namespace PharIo\Phive;

class PharActivatorLocator {

    /**
     * @var PharActivatorFactory
     */
    private $factory;

    /**
     * @param PharActivatorFactory $factory
     */
    public function __construct(PharActivatorFactory $factory) {
        $this->factory = $factory;
    }

    /**
     * @param Environment $environment
     *
     * @return PharActivator
     */
    public function getPharActivator(Environment $environment) {
        if ($environment instanceof WindowsEnvironment) {
            return $this->factory->getBatPharActivator();
        }
        return $this->factory->getSymlinkPharActivator();
    }
    
}