<?php declare(strict_types = 1);
namespace PharIo\Phive;

/**
 * Resolves a requested PHAR to potential Sources
 */
interface RequestedPharResolver {
    public function resolve(RequestedPhar $requestedPhar): SourceRepository;

    public function setNext(self $resolver): void;
}
