<?php declare(strict_types = 1);
namespace PharIo\Phive;

class CurlConfigBuilder {

    /** @var Environment */
    private $environment;

    /** @var PhiveVersion */
    private $phiveVersion;

    public function __construct(Environment $environment, PhiveVersion $phiveVersion) {
        $this->environment  = $environment;
        $this->phiveVersion = $phiveVersion;
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

        foreach ($this->environment->getAuthentications() as $authentication) {
            $curlConfig->addAuthenticationToken($authentication);
        }

        return $curlConfig;
    }
}
