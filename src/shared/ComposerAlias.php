<?php
namespace PharIo\Phive;

class ComposerAlias {

    /**
     * @var string
     */
    private $vendor;

    /**
     * @var string
     */
    private $name;

    /**
     * @param string $alias
     */
    public function __construct($alias) {
        $this->ensureValidFormat($alias);
        list($this->vendor, $this->name) = explode('/', $alias);
    }

    /**
     * @return string
     */
    public function __toString() {
        return $this->vendor . '/' . $this->name;
    }

    /**
     * @return string
     */
    public function getVendor() {
        return $this->vendor;
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @param $alias
     *
     * @throws \InvalidArgumentException
     */
    private function ensureValidFormat($alias) {
        $check = strpos($alias, '/');
        if ($check === false || $check === 0) {
            throw new \InvalidArgumentException(
                sprintf('Invalid composer alias, must be of format "vendor/name", "%s" given', $alias)
            );
        }
    }

}
