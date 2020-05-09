<?php declare(strict_types = 1);
namespace PharIo\Phive;

/**
 * Resolves an alias to a list of local repository entries
 */
class LocalAliasResolver extends AbstractRequestedPharResolver {
    /** @var PharRegistry */
    private $registry;

    public function __construct(PharRegistry $registry) {
        $this->registry = $registry;
    }

    public function resolve(RequestedPhar $requestedPhar): SourceRepository {
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
