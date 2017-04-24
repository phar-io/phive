<?php
namespace PharIo\Phive;

class UnsupportedHashStub implements Hash {

    /**
     * @return string
     */
    public function asString() {
        return 'foo';
    }

    public static function forContent($content) {
        return new static();
    }

    /**
     * @param Hash $otherHash
     *
     * @return bool
     */
    public function equals(Hash $otherHash) {
        return false;
    }

}


