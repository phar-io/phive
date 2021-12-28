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

class RequestedPharResolverService {
    /** @var null|RequestedPharResolver */
    private $first;

    /** @var null|RequestedPharResolver */
    private $last;

    /** @psalm-assert !null $this->first */
    public function addResolver(RequestedPharResolver $resolver): void {
        if ($this->first === null) {
            $this->first = $resolver;
        }

        if ($this->last !== null) {
            $this->last->setNext($resolver);
        }
        $this->last = $resolver;
    }

    public function resolve(RequestedPhar $requestedPhar): SourceRepository {
        if ($this->first === null) {
            throw new ResolveException('Call addResolver before executing resolve');
        }

        return $this->first->resolve($requestedPhar);
    }
}
