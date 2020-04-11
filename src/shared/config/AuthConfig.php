<?php declare(strict_types = 1);
namespace PharIo\Phive;

interface AuthConfig {
    public function hasAuthentication(string $domain): bool;

    public function getAuthentication(string $domain): Authentication;
}
