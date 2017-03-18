<?php
namespace PharIo\Phive;

/**
 * Resolves an alias to a list of local repository entries
 */
class LocalAliasResolver extends AbstractRequestedPharResolver {

    /**
     * @var PharRegistry
     */
    private $registry;

    /**
     * @param PharRegistry $registry
     */
    public function __construct(PharRegistry $registry) {
        $this->registry = $registry;
    }

    /**
     * @param RequestedPhar $requestedPhar
     *
     * @return SourceRepository
     */
    public function resolve(RequestedPhar $requestedPhar) {
        if (!$requestedPhar->hasAlias()) {
            return $this->tryNext($requestedPhar);
        }

        $repository = new LocalRepository($this->registry);

        if ($repository->getReleasesByRequestedPhar($requestedPhar)->count() > 0) {
            return $repository;
        }

        return $this->tryNext($requestedPhar);
    }

}
