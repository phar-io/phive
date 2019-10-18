<?php declare(strict_types = 1);
namespace PharIo\Phive;

class CurlConfig {

    /** @var string optional proxy URL */
    private $proxyUrl;

    /** @var string optional proxy credentials */
    private $proxyCredentials;

    /** @var string */
    private $userAgent;

    /** @var array */
    private $localSslCertificates = [];

    /** @var array */
    private $authenticationTokens = [];

    /** @var array<string,string> */
    private $hostMap = [];

    public function __construct(string $userAgent) {
        $this->userAgent = $userAgent;
    }

    public function setProxy(string $url, string $username = '', string $password = ''): void {
        $this->proxyUrl = $url;

        if ('' !== $username && '' !== $password) {
            $this->proxyCredentials = \sprintf('%s:%s', $username, $password);
        }
    }

    public function addLocalSslCertificate(LocalSslCertificate $certificate): void {
        $this->localSslCertificates[$certificate->getHostname()] = $certificate;
    }

    /**
     * @throws CurlConfigException
     */
    public function getLocalSslCertificate(string $hostname): LocalSslCertificate {
        if (!$this->hasLocalSslCertificate($hostname)) {
            throw new CurlConfigException(\sprintf('No local certificate for hostname %s found', $hostname));
        }

        return $this->localSslCertificates[$hostname];
    }

    public function hasLocalSslCertificate(string $hostname): bool {
        return \array_key_exists($hostname, $this->localSslCertificates);
    }

    public function asCurlOptArray(): array {
        $options = [
            \CURLOPT_MAXREDIRS       => 5,
            \CURLOPT_CONNECTTIMEOUT  => 60,
            \CURLOPT_SSL_VERIFYHOST  => 2,
            \CURLOPT_SSL_VERIFYPEER  => true,
            \CURLOPT_FAILONERROR     => false,
            \CURLOPT_RETURNTRANSFER  => true,
            \CURLOPT_FOLLOWLOCATION  => true,
            \CURLOPT_USERAGENT       => $this->userAgent,
            \CURLOPT_PROXY           => $this->proxyUrl,
            \CURLOPT_PROXYUSERPWD    => $this->proxyCredentials,
            \CURLOPT_LOW_SPEED_TIME  => 90,
            \CURLOPT_LOW_SPEED_LIMIT => 128,
            \CURLOPT_PROTOCOLS       => \CURLPROTO_HTTPS
        ];

        /* Added in PHP 7.0.7 and requires Curl 7.49+ */
        if (\defined('CURLOPT_TCP_FASTOPEN')) {
            $options[\CURLOPT_TCP_FASTOPEN] = true;
        }

        return $options;
    }

    /**
     * @throws CurlConfigException
     */
    public function addAuthenticationToken(string $hostname, string $token): void {
        if ($this->hasAuthenticationToken($hostname)) {
            throw new CurlConfigException(\sprintf('Authentication token for hostname %s already set', $hostname));
        }

        $this->authenticationTokens[$hostname] = $token;
    }

    public function hasAuthenticationToken(string $hostname): bool {
        return \array_key_exists($hostname, $this->authenticationTokens);
    }

    /**
     * @throws CurlConfigException
     */
    public function getAuthenticationToken(string $hostname): string {
        if (!$this->hasAuthenticationToken($hostname)) {
            throw new CurlConfigException(\sprintf('No authentication for hostname %s found', $hostname));
        }

        return $this->authenticationTokens[$hostname];
    }

    public function setResolvedIp(string $hostname, string $ip): void {
        $this->hostMap[$hostname] = $ip;
    }

    public function hasResolvedIp(string $hostname): bool {
        return isset($this->hostMap[$hostname]);
    }

    public function getResolvedIp(string $hostname): string {
        if (!$this->hasResolvedIp($hostname)) {
            throw new CurlConfigException(\sprintf('No resolved IP for hostname %s found', $hostname));
        }

        return $this->hostMap[$hostname];
    }
}
