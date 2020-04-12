<?php declare(strict_types = 1);
namespace PharIo\Phive;

class Authentication {

    /** @var string */
    private $type;
    /** @var string */
    private $credentials;
    /** @var string */
    private $domain;

    public static function fromLoginPassword(string $domain, string $login, string $password): Authentication {
        $credentials = \base64_encode($login . ':' . $password);

        return new static($domain, 'Basic', $credentials);
    }

    public function __construct(string $domain, string $type, string $credentials) {
        $this->type        = $type;
        $this->credentials = $credentials;
        $this->domain      = $domain;
    }

    public function asString(): string {
        return \sprintf('Authorization: %s %s', $this->type, $this->credentials);
    }

    public function getDomain(): string {
        return $this->domain;
    }
}
