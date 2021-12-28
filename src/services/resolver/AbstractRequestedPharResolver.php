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

use function sprintf;

abstract class AbstractRequestedPharResolver implements RequestedPharResolver {
    /** @var null|RequestedPharResolver */
    private $next;

    public function setNext(RequestedPharResolver $resolver): void {
        $this->next = $resolver;
    }

    abstract public function resolve(RequestedPhar $requestedPhar): SourceRepository;

    /**
     * @throws ResolveException
     */
    protected function tryNext(RequestedPhar $requestedPhar): SourceRepository {
        if ($this->next === null) {
            throw new ResolveException(sprintf('Could not resolve requested PHAR %s', $requestedPhar->getIdentifier()->asString()));
        }

        return $this->next->resolve($requestedPhar);
    }
}
