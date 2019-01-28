<?php declare(strict_types = 1);
namespace PharIo\Phive\Cli;

class CommandOptionsException extends \Exception {
    public const NoSuchOption = 1;

    public const InvalidArgumentIndex = 2;
}
