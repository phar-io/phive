<?php declare(strict_types = 1);
namespace PharIo\Phive;

class ErrorException extends \ErrorException implements Exception {
    /** @var array */
    private $context;

    public function __construct(string $message, int $code, int $severity, string $filename, int $lineno, array $context, \Throwable $previous = null) {
        parent::__construct($message, $code, $severity, $filename, $lineno, $previous);
        $this->context = $context;
    }

    public function getContext(): array {
        return $this->context;
    }
}
