<?php declare(strict_types = 1);
namespace PharIo\Phive;

class EnvironmentAuthConfig implements AuthConfig {
    private const KNOWN_TOKEN = [
        'api.github.com' => 'GITHUB_AUTH_TOKEN',
        'gitlab.com'     => 'GITLAB_AUTH_TOKEN',
    ];

    /** @var Environment */
    private $environment;

    public function __construct(Environment $environment) {
        $this->environment = $environment;
    }

    public function hasAuthentication(string $domain): bool {
        if (!\array_key_exists($domain, self::KNOWN_TOKEN)) {
            return false;
        }

        return $this->environment->hasVariable(self::KNOWN_TOKEN[$domain]);
    }

    /**
     * @throws AuthException
     * @throws \BadMethodCallException
     */
    public function getAuthentication(string $domain): Authentication {
        if (!$this->hasAuthentication($domain)) {
            throw new AuthException(\sprintf('No authentication data for %s', $domain));
        }

        $token = $this->environment->getVariable(self::KNOWN_TOKEN[$domain]);

        switch ($domain) {
            // "Token" Authorizations
            case 'api.github.com':
                return new Authentication($domain, 'Token', $token);
            // "Bearer" Authorizations
            case 'gitlab.com':
                return new Authentication($domain, 'Bearer', $token);
            default:
                throw new \BadMethodCallException('Unknown authentication');
        }
    }
}
