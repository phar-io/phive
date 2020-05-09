<?php declare(strict_types = 1);
namespace PharIo\Phive;

class InstallCommandConfigException extends \Exception implements Exception {
    public const UnsupportedProtocol = 1;
}
