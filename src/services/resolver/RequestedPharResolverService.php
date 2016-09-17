<?php
namespace PharIo\Phive;

class RequestedPharResolverService {

    /**
     * @var RequestedPharResolver
     */
    private $first;

    /**
     * @var RequestedPharResolver
     */
    private $last;

    /**
     * @param RequestedPharResolver $resolver
     */
    public function addResolver(RequestedPharResolver $resolver) {
        if ($this->first === null) {
            $this->first = $resolver;
        }
        if ($this->last !== null) {
            $this->last->setNext($resolver);
        }
        $this->last = $resolver;
    }

    /**
     * @param RequestedPhar $requestedPhar
     *
     * @return SourceRepository
     */
    public function resolve(RequestedPhar $requestedPhar) {
        return $this->first->resolve($requestedPhar);
    }

}
