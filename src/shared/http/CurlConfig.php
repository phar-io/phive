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

use const CURLOPT_CONNECTTIMEOUT;
use const CURLOPT_FAILONERROR;
use const CURLOPT_FOLLOWLOCATION;
use const CURLOPT_LOW_SPEED_LIMIT;
use const CURLOPT_LOW_SPEED_TIME;
use const CURLOPT_MAXREDIRS;
use const CURLOPT_PROTOCOLS;
use const CURLOPT_PROXY;
use const CURLOPT_PROXYUSERPWD;
use const CURLOPT_RETURNTRANSFER;
use const CURLOPT_SSL_VERIFYHOST;
use const CURLOPT_SSL_VERIFYPEER;
use const CURLOPT_TCP_FASTOPEN;
use const CURLOPT_USERAGENT;
use const CURLPROTO_HTTPS;
use function array_key_exists;
use function defined;
use function sprintf;

class CurlConfig {
    /** @var null|string optional proxy URL */
    private $proxyUrl;

    /** @var null|string optional proxy credentials */
    private $proxyCredentials;

    /** @var string */
    private $userAgent;

    /** @var array<string, LocalSslCertificate> */
    private $localSslCertificates = [];

    /** @var AuthConfig */
    private $authConfig;

    /** @var array<string,string> */
    private $hostMap = [];

    public function __construct(string $userAgent) {
        $this->userAgent = $userAgent;
    }

    public function setProxy(string $url, string $username = '', string $password = ''): void {
        $this->proxyUrl = $url;

        if ('' !== $username && '' !== $password) {
            $this->proxyCredentials = sprintf('%s:%s', $username, $password);
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
            throw new CurlConfigException(sprintf('No local certificate for hostname %s found', $hostname));
        }

        return $this->localSslCertificates[$hostname];
    }

    public function hasLocalSslCertificate(string $hostname): bool {
        return array_key_exists($hostname, $this->localSslCertificates);
    }

    public function asCurlOptArray(): array {
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
            CURLOPT_LOW_SPEED_LIMIT => 128,
            CURLOPT_PROTOCOLS       => CURLPROTO_HTTPS
        ];

        /* Added in PHP 7.0.7 and requires Curl 7.49+ */
        if (defined('CURLOPT_TCP_FASTOPEN')) {
            $options[CURLOPT_TCP_FASTOPEN] = true;
        }

        return $options;
    }

    public function setAuthConfig(AuthConfig $authConfig): void {
        $this->authConfig = $authConfig;
    }

    public function hasAuthentication(string $hostname): bool {
        return $this->authConfig->hasAuthentication($hostname);
    }

    /**
     * @throws CurlConfigException
     */
    public function getAuthentication(string $hostname): Authentication {
        if (!$this->hasAuthentication($hostname)) {
            throw new CurlConfigException(sprintf('No authentication for hostname %s found', $hostname));
        }

        return $this->authConfig->getAuthentication($hostname);
    }

    public function setResolvedIp(string $hostname, string $ip): void {
        $this->hostMap[$hostname] = $ip;
    }

    public function hasResolvedIp(string $hostname): bool {
        return isset($this->hostMap[$hostname]);
    }

    public function getResolvedIp(string $hostname): string {
        if (!$this->hasResolvedIp($hostname)) {
            throw new CurlConfigException(sprintf('No resolved IP for hostname %s found', $hostname));
        }

        return $this->hostMap[$hostname];
    }
}
