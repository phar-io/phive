<?php declare(strict_types = 1);
namespace PharIo\Phive;

class BearerAuthentication extends Authentication {
    protected function getType(): string {
        return 'Bearer';
    }
}
