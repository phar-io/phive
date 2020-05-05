<?php declare(strict_types = 1);
namespace PharIo\Phive;

class ExtensionsMissingException extends \Exception {
    /** @var array */
    private $missing;

    /**
     * ExtensionsMissingException constructor.
     */
    public function __construct(array $missing) {
        $this->missing = $missing;
        parent::__construct();
    }

    public function getMissing(): array {
        return $this->missing;
    }
}
