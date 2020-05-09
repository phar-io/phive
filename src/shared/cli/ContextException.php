<?php declare(strict_types = 1);
namespace PharIo\Phive\Cli;

use PharIo\Phive\Exception;

class ContextException extends \Exception implements Exception {
    public const ConflictingOptions = 1;
}
