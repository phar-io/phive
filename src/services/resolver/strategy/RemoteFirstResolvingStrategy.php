<?php declare(strict_types = 1);
namespace PharIo\Phive;

class RemoteFirstResolvingStrategy extends AbstractResolvingStrategy {
    public function execute(RequestedPharResolverService $service): void {
        parent::execute($service);
        $service->addResolver($this->getFactory()->getLocalAliasResolver());
    }
}
