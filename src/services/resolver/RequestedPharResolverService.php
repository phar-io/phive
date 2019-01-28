<?php declare(strict_types = 1);
namespace PharIo\Phive;

class RequestedPharResolverService {
    /** @var RequestedPharResolver */
    private $first;

    /** @var RequestedPharResolver */
    private $last;

    public function addResolver(RequestedPharResolver $resolver): void {
        if ($this->first === null) {
            $this->first = $resolver;
        }

        if ($this->last !== null) {
            $this->last->setNext($resolver);
        }
        $this->last = $resolver;
    }

    public function resolve(RequestedPhar $requestedPhar): SourceRepository {
        return $this->first->resolve($requestedPhar);
    }
}
