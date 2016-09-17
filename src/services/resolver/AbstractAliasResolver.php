<?php
namespace PharIo\Phive;

abstract class AbstractAliasResolver implements AliasResolver {

    /**
     * @var AliasResolver
     */
    private $next;

    /**
     * @param AliasResolver $resolver
     */
    public function setNext(AliasResolver $resolver) {
        $this->next = $resolver;
    }

    /**
     * @param PharAlias $alias
     *
     * @return SourceRepository
     * @throws ResolveException
     */
    protected function tryNext(PharAlias $alias) {
        if ($this->next === null) {
            throw new ResolveException(sprintf('Could not resolve alias %s', $alias));
        }
        return $this->next->resolve($alias);
    }

    /**
     * @param PharAlias $alias
     *
     * @return SourceRepository
     */
    abstract public function resolve(PharAlias $alias);
}
