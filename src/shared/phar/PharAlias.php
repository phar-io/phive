<?php declare(strict_types = 1);
namespace PharIo\Phive;

class PharAlias implements PharIdentifier {

    /** @var string */
    private $alias;

    /**
     * @param string $alias
     */
    public function __construct($alias) {
        $this->alias = $alias;
    }

    public function asString(): string {
        return $this->alias;
    }
}
