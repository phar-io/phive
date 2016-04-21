<?php
namespace PharIo\Phive;

/**
 * Resolves an alias to potential Sources
 */
interface AliasResolver {

    /**
     * @param PharAlias $alias
     *
     * @return Source[]
     * @throws ResolveException
     */
    public function resolve(PharAlias $alias);

    /**
     * @param AliasResolver $resolver
     */
    public function setNext(AliasResolver $resolver);
}
