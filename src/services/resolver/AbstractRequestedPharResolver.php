<?php declare(strict_types = 1);
namespace PharIo\Phive;

abstract class AbstractRequestedPharResolver implements RequestedPharResolver {
    /** @var null|RequestedPharResolver */
    private $next;

    public function setNext(RequestedPharResolver $resolver): void {
        $this->next = $resolver;
    }

    abstract public function resolve(RequestedPhar $requestedPhar): SourceRepository;

    /**
     * @throws ResolveException
     */
    protected function tryNext(RequestedPhar $requestedPhar): SourceRepository {
        if ($this->next === null) {
            throw new ResolveException(\sprintf('Could not resolve requested PHAR %s', $requestedPhar->getIdentifier()->asString()));
        }

        return $this->next->resolve($requestedPhar);
    }
}
