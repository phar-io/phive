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

class CurlConfigBuilder {
    /** @var Environment */
    private $environment;

    /** @var PhiveVersion */
    private $phiveVersion;

    /** @var AuthConfig */
    private $authConfig;

    public function __construct(Environment $environment, PhiveVersion $phiveVersion, AuthConfig $authConfig) {
        $this->environment  = $environment;
        $this->phiveVersion = $phiveVersion;
        $this->authConfig   = $authConfig;
    }

    public function build(): CurlConfig {
        $curlConfig = new CurlConfig(
            sprintf(
                'Phive %s on %s',
                $this->phiveVersion->getVersion(),
                $this->environment->getRuntimeString()
            )
        );

        if ($this->environment->hasProxy()) {
            $curlConfig->setProxy($this->environment->getProxy());
        }

        $curlConfig->setAuthConfig($this->authConfig);

        return $curlConfig;
    }
}
