<?php
namespace PharIo\Phive;

class CurlConfigBuilder {

    /**
     * @var Environment
     */
    private $environment;

    /**
     * @var PhiveVersion
     */
    private $phiveVersion;

    /**
     * @param Environment $environment
     * @param PhiveVersion $phiveVersion
     */
    public function __construct(Environment $environment, PhiveVersion $phiveVersion) {
        $this->environment = $environment;
        $this->phiveVersion = $phiveVersion;
    }

    public function build() {
        $curlConfig = new CurlConfig(
            sprintf('Phive %s on %s',
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
        if ($this->environment->hasGitHubAuthToken()) {
            $curlConfig->addAuthenticationToken(
                'github.com',
                $this->environment->getGitHubAuthToken()
            );
        }

        return $curlConfig;
    }

}
