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

class RequestedPharResolverServiceBuilder {
    /** @var Factory */
    private $factory;

    public function __construct(Factory $factory) {
        $this->factory = $factory;
    }

    public function build(ResolvingStrategy $strategy): RequestedPharResolverService {
        $service = $this->factory->getRequestedPharResolverService();
        $strategy->execute($service);

        return $service;
    }
}
