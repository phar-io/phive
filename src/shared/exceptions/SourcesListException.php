<?php declare(strict_types = 1);
namespace PharIo\Phive;

class SourcesListException extends \Exception implements Exception {
    public const ComposerAliasNotFound = 1;
}
