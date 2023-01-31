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

use function in_array;
use function sprintf;
use InvalidArgumentException;

class Source {
    /** @var string */
    private $type;

    /** @var Url */
    private $url;

    public function __construct(string $type, Url $url) {
        $this->ensureValidSourceType($type);
        $this->type = $type;
        $this->url  = $url;
    }

    public function getType(): string {
        return $this->type;
    }

    public function getUrl(): Url {
        return $this->url;
    }

    private function ensureValidSourceType(string $type): void {
        if (!in_array($type, ['phar.io', 'github', 'gitlab'], true)) {
            throw new InvalidArgumentException(
                sprintf('Unsupported source repository type "%s"', $type)
            );
        }
    }
}
