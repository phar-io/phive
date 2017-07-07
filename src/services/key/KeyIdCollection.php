<?php
namespace PharIo\Phive;

class KeyIdCollection {

    /**
     * @var array
     */
    private $keyIds = [];

    /**
     * @param $keyId
     */
    public function addKeyId($keyId) {
        $this->keyIds[] = $keyId;
    }

    /**
     * @param $keyId
     *
     * @return bool
     */
    public function has($keyId) {
        return in_array($keyId, $this->keyIds, true);
    }
}
