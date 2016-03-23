<?php
namespace PharIo\Phive;

class ExtensionsMissingException extends \Exception {

    /**
     * @var array
     */
    private $missing;

    /**
     * ExtensionsMissingException constructor.
     *
     * @param array $missing
     *
     */
    public function __construct(array $missing) {
        $this->missing = $missing;
        parent::__construct();
    }

    /**
     * @return array
     */
    public function getMissing() {
        return $this->missing;
    }

}
