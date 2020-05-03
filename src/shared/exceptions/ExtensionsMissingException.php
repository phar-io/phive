<?php declare(strict_types = 1);
namespace PharIo\Phive;

class ExtensionsMissingException extends \Exception {
    /** @var array */
    private $missing;

    /**
     * ExtensionsMissingException constructor.
     */
    public function __construct(array $failed) {
        $this->missing = $failed;
        parent::__construct();
    }

    public function getMissing(): array {
        return $this->missing;
    }
}
