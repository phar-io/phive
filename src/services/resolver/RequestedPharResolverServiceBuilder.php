<?php
namespace PharIo\Phive;

class RequestedPharResolverServiceBuilder {
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
     * @param ResolvingStrategy $strategy
     *
     * @return RequestedPharResolverService
     */
    public function build(ResolvingStrategy $strategy) {
        $service = $this->factory->getRequestedPharResolverService();
        $strategy->execute($service);

        return $service;
    }

}
