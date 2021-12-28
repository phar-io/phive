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

use function count;
use ArrayIterator;
use Countable;
use IteratorAggregate;
use Traversable;

class ReleaseCollection implements Countable, IteratorAggregate {
    /** @var Release[] */
    private $releases = [];

    public function add(Release $release): void {
        $this->releases[] = $release;
    }

    public function count(): int {
        return count($this->releases);
    }

    public function getIterator(): Traversable {
        return new ArrayIterator($this->releases);
    }
}
