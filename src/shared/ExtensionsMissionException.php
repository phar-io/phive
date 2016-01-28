<?php
namespace PharIo\Phive;

class ExtensionsMissionException extends \Exception {

    /**
     * @var array
     */
    private $mising;

    /**
     * ExtensionsMissionException constructor.
     *
     * @param array $mising
     */
    public function __construct(array $mising) {
        $this->mising = $mising;
    }

    /**
     * @return array
     */
    public function getMising() {
        return $this->mising;
    }

}
