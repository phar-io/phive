<?php declare(strict_types = 1);
namespace PharIo\Phive;

interface ResolvingStrategy {
    public function execute(RequestedPharResolverService $pharResolverService): void;
}
