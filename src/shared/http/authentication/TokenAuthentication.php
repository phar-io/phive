<?php declare(strict_types = 1);
namespace PharIo\Phive;

class TokenAuthentication extends Authentication {
    protected function getType(): string {
        return 'Token';
    }
}
