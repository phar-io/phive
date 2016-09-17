<?php
namespace PharIo\Phive;

/**
 * Resolves a requested PHAR to potential Sources
 */
interface RequestedPharResolver {

    /**
     * @param RequestedPhar $requestedPhar
     *
     * @return SourceRepository
     */
    public function resolve(RequestedPhar $requestedPhar);

    /**
     * @param RequestedPharResolver $resolver
     */
    public function setNext(RequestedPharResolver $resolver);
}
