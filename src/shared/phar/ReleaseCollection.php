<?php declare(strict_types = 1);
namespace PharIo\Phive;

class ReleaseCollection implements \Countable, \IteratorAggregate {
    /** @var Release[] */
    private $releases = [];

    public function add(Release $release): void {
        $this->releases[] = $release;
    }

    public function count(): int {
        return \count($this->releases);
    }

    public function getIterator() {
        return new \ArrayIterator($this->releases);
    }
}
