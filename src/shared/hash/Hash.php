<?php
namespace PharIo\Phive;

interface Hash {

    public function asString();

    /**
     * @param Hash $otherHash
     *
     * @return bool
     */
    public function equals(Hash $otherHash);
}


