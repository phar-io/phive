<?php
namespace PharIo\Phive;

use \InvalidArgumentException;

class JsonData {

    /**
     * @var string
     */
    private $raw;

    /**
     * @var array
     */
    private $parsed;

    /**
     * JsonData constructor.
     *
     * @param string $raw
     */
    public function __construct($raw) {
        $this->raw = $raw;
        $this->parsed = json_decode($raw, true);
        if (json_last_error() != JSON_ERROR_NONE) {
            throw new InvalidArgumentException(json_last_error_msg(), json_last_error());
        }
    }

}
