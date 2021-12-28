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

use function sprintf;

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

        throw new AuthException(sprintf('No authentication data for %s', $domain));
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
