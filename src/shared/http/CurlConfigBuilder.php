<?php declare(strict_types = 1);
namespace PharIo\Phive;

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
            \sprintf(
                'Phive %s on %s',
                $this->phiveVersion->getVersion(),
                $this->environment->getRuntimeString()
            )
        );
        $curlConfig->addLocalSslCertificate(
            new LocalSslCertificate(
                'hkps.pool.sks-keyservers.net',
                __DIR__ . '/../../../conf/ssl/ca_certs/sks-keyservers.netCA.pem'
            )
        );

        if ($this->environment->hasProxy()) {
            $curlConfig->setProxy($this->environment->getProxy());
        }

        $curlConfig->setAuthConfig($this->authConfig);

        return $curlConfig;
    }
}
