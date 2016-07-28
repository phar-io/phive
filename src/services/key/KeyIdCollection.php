<?php
namespace PharIo\Phive;

class KeyIdCollection {

    private $keyIds = [];

    public function addKeyId($keyId) {
        $this->keyIds[] = $keyId;
    }

    public function has($keyId) {
        return in_array($keyId, $this->keyIds, true);
    }
}
