<?php
namespace PharIo\Phive;

class ReleaseCollection implements \Countable, \IteratorAggregate {

    /**
     * @var Release[]
     */
    private $releases = [];

    /**
     * @param Release $release
     */
    public function add(Release $release) {
        $this->releases[] = $release;
    }

    /**
     * @return int
     */
    public function count() {
        return count($this->releases);
    }

    public function getIterator() {
        return new \ArrayIterator($this->releases);
    }

}
