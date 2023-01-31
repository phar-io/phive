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

abstract class Authentication {
    /** @var string */
    private $credentials;

    public function __construct(string $credentials) {
        $this->credentials = $credentials;
    }

    public function asHttpHeaderString(): string {
        return sprintf('Authorization: %s %s', $this->getType(), $this->credentials);
    }

    abstract protected function getType(): string;
}
