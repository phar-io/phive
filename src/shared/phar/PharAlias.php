<?php
namespace PharIo\Phive;

class PharAlias implements PharIdentifier {

    /**
     * @var string
     */
    private $alias = '';

    /**
     * @param string $alias
     */
    public function __construct($alias) {
        $this->alias = $alias;
    }

    /**
     * @return string
     */
    public function asString() {
        return $this->alias;
    }

}
