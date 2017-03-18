<?php
namespace PharIo\Phive;

class LocalFirstResolvingStrategy extends AbstractResolvingStrategy {

    /**
     * @param RequestedPharResolverService $service
     */
    public function execute(RequestedPharResolverService $service) {
        $service->addResolver($this->getFactory()->getLocalAliasResolver());
        parent::execute($service);
    }

}
