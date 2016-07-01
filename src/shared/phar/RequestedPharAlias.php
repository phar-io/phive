<?php
namespace PharIo\Phive;

class RequestedPharAlias implements RequestedPhar {

    /**
     * @var PharAlias
     */
    private $alias;

    /**
     * @param PharAlias $alias
     */
    public function __construct(PharAlias $alias) {
        $this->alias = $alias;
    }    
    
    /**
     * @return bool
     */
    public function isAlias() {
        return true;
    }

    /**
     * @return PharAlias
     */
    public function getAlias() {
        return $this->alias;
    }

    /**
     * @return null
     */
    public function getPharUrl() {
        return null;
    }
    
}
