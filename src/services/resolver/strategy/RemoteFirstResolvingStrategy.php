<?php
namespace PharIo\Phive;

class RemoteFirstResolvingStrategy extends AbstractResolvingStrategy {

    /**
     * @param RequestedPharResolverService $service
     */
    public function execute(RequestedPharResolverService $service) {
        parent::execute($service);
        $service->addResolver($this->getFactory()->getLocalAliasResolver());
    }
}
