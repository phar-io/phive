<?php
namespace PharIo\Phive;

class RequestedPharUrl implements RequestedPhar {

    /**
     * @var Url
     */
    private $url;

    /**
     * @param Url $url
     */
    public function __construct(Url $url) {
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
     * @return Url
     */
    public function getPharUrl() {
        return $this->url;
    }
    
}
