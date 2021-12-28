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

use function explode;
use function sprintf;
use function strpos;
use InvalidArgumentException;

class ComposerAlias {
    /** @var string */
    private $vendor;

    /** @var string */
    private $name;

    public function __construct(string $alias) {
        $this->ensureValidFormat($alias);
        [$this->vendor, $this->name] = explode('/', $alias);
    }

    public function asString(): string {
        return $this->vendor . '/' . $this->name;
    }

    public function getVendor(): string {
        return $this->vendor;
    }

    public function getName(): string {
        return $this->name;
    }

    /**
     * @param string $alias
     *
     * @throws InvalidArgumentException
     */
    private function ensureValidFormat($alias): void {
        $check = strpos($alias, '/');

        if ($check === false || $check === 0) {
            throw new InvalidArgumentException(
                sprintf('Invalid composer alias, must be of format "vendor/name", "%s" given', $alias)
            );
        }
    }
}
