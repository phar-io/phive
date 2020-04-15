<?php declare(strict_types = 1);
namespace PharIo\Phive;

class BasicAuthentication extends Authentication {
    public static function fromLoginPassword(string $domain, string $login, string $password): BasicAuthentication {
        $credentials = \base64_encode($login . ':' . $password);

        return new static($domain,  $credentials);
    }

    protected function getType(): string {
        return 'Basic';
    }
}
