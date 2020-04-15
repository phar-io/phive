<?php declare(strict_types = 1);
namespace PharIo\Phive;

abstract class Authentication {

    /** @var string */
    private $credentials;
    /** @var string */
    private $domain;

    public function __construct(string $domain, string $credentials) {
        $this->credentials = $credentials;
        $this->domain      = $domain;
    }

    public function asHttpHeaderString(): string {
        return \sprintf('Authorization: %s %s', $this->getType(), $this->credentials);
    }

    public function getDomain(): string {
        return $this->domain;
    }

    abstract protected function getType(): string;
}
