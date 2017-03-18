<?php
namespace PharIo\Phive;

interface ResolvingStrategy {

    /**
     * @param RequestedPharResolverService $pharResolverService
     */
    public function execute(RequestedPharResolverService $pharResolverService);

}
