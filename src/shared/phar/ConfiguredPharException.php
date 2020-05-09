<?php declare(strict_types = 1);
namespace PharIo\Phive;

class ConfiguredPharException extends \Exception implements Exception {
    public const NoLocation = 1;

    public const NoUrl = 2;
}
