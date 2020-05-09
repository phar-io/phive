<?php declare(strict_types = 1);
namespace PharIo\Phive\Cli;

class RequestException extends \Exception {
    public const UnexpectedArgument = 1;

    public const InvalidOption = 2;

    public const ValueRequired = 3;
}
