<?php
namespace PharIo\Phive;

class AliasResolverService {

    /**
     * @var AliasResolver
     */
    private $first;

    /**
     * @var AliasResolver
     */
    private $last;

    /**
     * @param AliasResolver $resolver
     */
    public function addResolver(AliasResolver $resolver) {
        if ($this->first === null) {
            $this->first = $resolver;
        }
        if ($this->last !== null) {
            $this->last->setNext($resolver);
        }
        $this->last = $resolver;
    }

    /**
     * @param PharAlias $alias
     *
     * @return SourceRepository
     */
    public function resolve(PharAlias $alias) {
        return $this->first->resolve($alias);
    }

}
