<?php
namespace PharIo\Phive;

class CurlConfig {

    /**
     * @var string optional proxy URL
     */
    private $proxyUrl;

    /**
     * @var string optional proxy credentials
     */
    private $proxyCredentials;

    /**
     * @var string
     */
    private $userAgent = '';

    /**
     * @var array
     */
    private $localSslCertificates = [];

    /**
     * @var array
     */
    private $authenticationTokens = [];

    /**
     * @param string $userAgent
     */
    public function __construct($userAgent) {
        $this->userAgent = $userAgent;
    }

    /**
     * @param string $url
     * @param string $username
     * @param string $password
     */
    public function setProxy($url, $username = '', $password = '') {
        $this->proxyUrl = $url;
        if ('' !== $username && '' !== $password) {
            $this->proxyCredentials = sprintf('%s:%s', $username, $password);
        }
    }

    /**
     * @param LocalSslCertificate $certificate
     */
    public function addLocalSslCertificate(LocalSslCertificate $certificate) {
        $this->localSslCertificates[$certificate->getHostname()] = $certificate;
    }

    /**
     * @param string $hostname
     *
     * @return LocalSslCertificate
     * @throws CurlConfigException
     */
    public function getLocalSslCertificate($hostname) {
        if (!$this->hasLocalSslCertificate($hostname)) {
            throw new CurlConfigException(sprintf('No local certificate for hostname %s found', $hostname));
        }

        return $this->localSslCertificates[$hostname];
    }

    /**
     * @param string $hostname
     *
     * @return bool
     *
     */
    public function hasLocalSslCertificate($hostname) {
        return array_key_exists($hostname, $this->localSslCertificates);
    }

    /**
     * @return array
     */
    public function asCurlOptArray() {
        $options = [
            CURLOPT_MAXREDIRS       => 5,
            CURLOPT_CONNECTTIMEOUT  => 60,
            CURLOPT_SSL_VERIFYHOST  => 2,
            CURLOPT_SSL_VERIFYPEER  => true,
            CURLOPT_FAILONERROR     => false,
            CURLOPT_RETURNTRANSFER  => true,
            CURLOPT_FOLLOWLOCATION  => true,
            CURLOPT_USERAGENT       => $this->userAgent,
            CURLOPT_PROXY           => $this->proxyUrl,
            CURLOPT_PROXYUSERPWD    => $this->proxyCredentials,
            CURLOPT_LOW_SPEED_TIME  => 90,
            CURLOPT_LOW_SPEED_LIMIT => 128
        ];

        /*
         * CURLOPT_PROTOCOLS is not available in older versions of HHVM,
         * so we explicitly have to check if it is defined.
         * See https://github.com/facebook/hhvm/issues/3702
         */
        if (defined('CURLOPT_PROTOCOLS')) {
            $options[CURLOPT_PROTOCOLS] = CURLPROTO_HTTPS;
        }

        /* Added in PHP 7.0.7 and requires Curl 7.49+ */
        if (defined('CURLOPT_TCP_FASTOPEN')) {
            $options[CURLOPT_TCP_FASTOPEN] = true;
        }

        return $options;
    }

    /**
     * @param string $hostname
     * @param string $token
     *
     * @throws CurlConfigException
     */
    public function addAuthenticationToken($hostname, $token) {
        if ($this->hasAuthenticationToken($hostname)) {
            throw new CurlConfigException(sprintf('Authentication token for hostname %s already set', $hostname));
        }

        $this->authenticationTokens[$hostname] = $token;
    }

    /**
     * @param string $hostname
     *
     * @return bool
     */
    public function hasAuthenticationToken($hostname) {
        return array_key_exists($hostname, $this->authenticationTokens);
    }

    /**
     * @param string $hostname
     *
     * @return string
     *
     * @throws CurlConfigException
     */
    public function getAuthenticationToken($hostname) {
        if (!$this->hasAuthenticationToken($hostname)) {
            throw new CurlConfigException(sprintf('No authentication for hostname %s found', $hostname));
        }

        return $this->authenticationTokens[$hostname];
    }

}
