<?php
namespace PharIo\Phive;

class ETag {

    private $value;

    /**
     * ETag constructor.
     *
     * @param $value
     */
    public function __construct($value) {
        $this->value = $value;
    }

    /**
     * @return mixed
     */
    public function asString() {
        return $this->value;
    }

}
