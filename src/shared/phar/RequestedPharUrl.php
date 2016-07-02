<?php
namespace PharIo\Phive;

class RequestedPharUrl implements RequestedPhar {

    /**
     * @var PharUrl
     */
    private $url;

    /**
     * @param PharUrl $url
     */
    public function __construct(PharUrl $url) {
        $this->url = $url;
    }

    /**
     * @return bool
     */
    public function isAlias() {
        return false;
    }

    /**
     * @return null
     */
    public function getAlias() {
        return null;
    }

    /**
     * @return PharUrl
     */
    public function getPharUrl() {
        return $this->url;
    }

}
