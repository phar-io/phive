<?php declare(strict_types = 1);
namespace PharIo\Phive;

class CompositeAuthConfig implements AuthConfig {
    /** @var AuthConfig[] */
    private $authConfigs;

    /**
     * @param AuthConfig[] $authConfigs
     */
    public function __construct(array $authConfigs) {
        $this->authConfigs = $authConfigs;
    }

    public function getAuthentication(string $domain): Authentication {
        foreach ($this->authConfigs as $authConfig) {
            if ($authConfig->hasAuthentication($domain)) {
                return $authConfig->getAuthentication($domain);
            }
        }

        throw new AuthException(\sprintf('No authentication data for %s', $domain));
    }

    public function hasAuthentication(string $domain): bool {
        foreach ($this->authConfigs as $authConfig) {
            if ($authConfig->hasAuthentication($domain)) {
                return true;
            }
        }

        return false;
    }
}
