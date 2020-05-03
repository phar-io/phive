<?php declare(strict_types = 1);
namespace PharIo\Phive;

class MigrationsFailedException extends \Exception {
    /** @var array */
    private $failed;

    public function __construct(array $failed) {
        $this->failed = $failed;
        parent::__construct();
    }

    public function getFailed(): array {
        return $this->failed;
    }
}
