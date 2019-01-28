<?php declare(strict_types = 1);
namespace PharIo\Phive;

class DirectoryException extends \Exception implements Exception {
    public const InvalidMode = 1;

    public const CreateFailed = 2;

    public const ChmodFailed = 3;

    public const InvalidType = 4;
}
