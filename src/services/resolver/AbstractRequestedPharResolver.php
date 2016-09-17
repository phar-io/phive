<?php
namespace PharIo\Phive;

abstract class AbstractRequestedPharResolver implements RequestedPharResolver {

    /**
     * @var RequestedPharResolver
     */
    private $next;

    /**
     * @param RequestedPharResolver $resolver
     */
    public function setNext(RequestedPharResolver $resolver) {
        $this->next = $resolver;
    }

    /**
     * @param RequestedPhar $requestedPhar
     *
     * @return SourceRepository
     * @throws ResolveException
     */
    protected function tryNext(RequestedPhar $requestedPhar) {
        if ($this->next === null) {
            throw new ResolveException(sprintf('Could not resolve requested PHAR %s', $requestedPhar->getIdentifier()->asString()));
        }
        return $this->next->resolve($requestedPhar);
    }

    /**
     * @param RequestedPhar $requestedPhar
     *
     * @return SourceRepository
     */
    abstract public function resolve(RequestedPhar $requestedPhar);
}
