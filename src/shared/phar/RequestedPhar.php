<?php
namespace PharIo\Phive;

interface RequestedPhar {
    
    /**
     * @return bool
     */
    public function isAlias();
    
    /**
     * @return PharAlias
     */
    public function getAlias();

    /**
     * @return Url
     */
    public function getPharUrl();
    
}
