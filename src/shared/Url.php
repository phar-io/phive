<?php
namespace PharIo\Phive;

class Url {

    /**
     * @var string
     */
    private $uri;

    /**
     * @param string $uri
     */
    public function __construct($uri) {
        if (strpos(strtolower($uri), 'https://') !== 0) {
            throw new \InvalidArgumentException('Only HTTPS protocol type supported');
        }
        $this->uri = $uri;
    }

    /**
     * @return string
     */
    public function __toString() {
        return $this->uri;
    }

}


