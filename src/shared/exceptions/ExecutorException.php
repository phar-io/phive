<?php declare(strict_types = 1);
namespace PharIo\Phive;

class ExecutorException extends \Exception implements Exception {
    public const NotFound = 1;

    public const NotExecutable = 2;
}
