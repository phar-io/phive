<?php declare(strict_types=1);
/*
 * This file is part of Phive.
 *
 * Copyright (c) Arne Blankerts <arne@blankerts.de>, Sebastian Heuer <sebastian@phpeople.de> and contributors
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */
namespace PharIo\Phive;

/**
 * Resolves an alias to a list of local repository entries.
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
