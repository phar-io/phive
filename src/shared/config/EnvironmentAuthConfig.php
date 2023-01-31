<?php declare(strict_types=1);
/*
 * This file is part of Phive.
 *
 * Copyright (c) Arne Blankerts <arne@blankerts.de>, Sebastian Heuer <sebastian@phpeople.de> and contributors
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */
namespace PharIo\Phive;

use function array_key_exists;
use function sprintf;
use BadMethodCallException;

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
        if (!array_key_exists($domain, self::KNOWN_TOKEN)) {
            return false;
        }

        return $this->environment->hasVariable(self::KNOWN_TOKEN[$domain]);
    }

    /**
     * @throws AuthException
     * @throws BadMethodCallException
     */
    public function getAuthentication(string $domain): Authentication {
        if (!$this->hasAuthentication($domain)) {
            throw new AuthException(sprintf('No authentication data for %s', $domain));
        }

        $token = $this->environment->getVariable(self::KNOWN_TOKEN[$domain]);

        switch ($domain) {
            case 'api.github.com':
                return new TokenAuthentication($token);

            case 'gitlab.com':
                return new BearerAuthentication($token);

            default:
                throw new BadMethodCallException('Unknown authentication');
        }
    }
}
