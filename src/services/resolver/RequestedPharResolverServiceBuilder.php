<?php declare(strict_types = 1);
namespace PharIo\Phive;

class RequestedPharResolverServiceBuilder {
    /** @var Factory */
    private $factory;

    public function __construct(Factory $factory) {
        $this->factory = $factory;
    }

    public function build(ResolvingStrategy $strategy): RequestedPharResolverService {
        $service = $this->factory->getRequestedPharResolverService();
        $strategy->execute($service);

        return $service;
    }
}
