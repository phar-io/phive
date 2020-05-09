<?php declare(strict_types = 1);
namespace PharIo\Phive;

class LocalFirstResolvingStrategy extends AbstractResolvingStrategy {
    public function execute(RequestedPharResolverService $service): void {
        $service->addResolver($this->getFactory()->getLocalAliasResolver());
        parent::execute($service);
    }
}
