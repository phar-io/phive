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

use function array_intersect;
use function array_keys;
use function count;
use function explode;
use function in_array;
use function mb_strlen;
use function preg_match;
use function sprintf;
use function strtolower;
use function trim;
use function ucfirst;
use DateTimeImmutable;

class CurlHttpClient implements HttpClient {
    /** @var CurlConfig */
    private $config;

    /** @var HttpProgressHandler */
    private $progressHandler;

    /** @var Url */
    private $url;

    /** @var null|ETag */
    private $etag;

    /** @var array */
    private $rateLimitHeaders = [];

    /** @var Curl */
    private $curl;

    public function __construct(
        CurlConfig $curlConfig,
        HttpProgressHandler $progressHandler,
        Curl $curlFunctions
    ) {
        $this->config          = $curlConfig;
        $this->progressHandler = $progressHandler;
        $this->curl            = $curlFunctions;
    }

    /**
     * @throws HttpException
     */
    public function head(Url $url, ?ETag $etag = null): HttpResponse {
        $this->url  = $url;
        $this->etag = $etag;

        $this->setupCurlInstance();
        $this->curl->doNotReturnBody();
        $this->curl->disableProgressMeter();

        return $this->execRequest();
    }

    /**
     * @throws HttpException
     */
    public function get(Url $url, ?ETag $etag = null): HttpResponse {
        $this->url  = $url;
        $this->etag = $etag;

        $this->progressHandler->start($url);
        $this->setupCurlInstance();
        $result = $this->execRequest();
        $this->progressHandler->finished();

        return $result;
    }

    /**
     * @param resource $ch
     */
    public function handleProgressInfo($ch, int $expectedDown, int $received, int $expectedUp, int $sent): int {
        $httpCode = $this->curl->getHttpCode();

        if ($httpCode >= 400) {
            return 0;
        }

        return $this->progressHandler->handleUpdate(
            new HttpProgressUpdate($this->url, $expectedDown, $received, $expectedUp, $sent)
        ) ? 0 : 1;
    }

    /**
     * @param resource $ch
     */
    public function handleHeaderInput($ch, string $line): int {
        $parts = explode(':', trim($line));

        if (!isset($parts[1])) {
            return mb_strlen($line);
        }

        [$header, $value] = $parts;
        $header           = ucfirst(strtolower($header));
        $value            = trim($value);

        if ($header === 'Etag') {
            $this->etag = new ETag($value);
        } elseif (preg_match('/^(X-)?RateLimit-(.*)$/i', $header, $matches) === 1) {
            $this->rateLimitHeaders[ucfirst(strtolower($matches[2]))] = $value;
        }

        return mb_strlen($line);
    }

    private function setupCurlInstance(): void {
        $this->curl->init($this->url->asString());

        $this->curl->setOptArray($this->config->asCurlOptArray());
        $this->curl->enableProgressMeter([$this, 'handleProgressInfo']);
        $this->curl->setHeaderFunction([$this, 'handleHeaderInput']);

        $headers = [];

        if ($this->etag !== null) {
            $headers[] = 'If-None-Match: ' . $this->etag->asString();
        }

        $hostname = $this->url->getHostname();

        if ($this->config->hasLocalSslCertificate($hostname)) {
            $this->curl->setCertificateFile($this->config->getLocalSslCertificate($hostname)->getCertificateFile());
        }

        if ($this->config->hasResolvedIp($hostname)) {
            $this->curl->setResolve($hostname . ':443:' . $this->config->getResolvedIp($hostname));
        }

        if ($this->config->hasAuthentication($hostname)) {
            $headers[] = $this->config->getAuthentication($hostname)->asHttpHeaderString();
        }

        if (count($headers) > 0) {
            $this->curl->addHttpHeaders($headers);
        }
    }

    /**
     * @throws HttpException
     */
    private function execRequest(): HttpResponse {
        $this->rateLimitHeaders = [];

        try {
            $result = $this->curl->exec();
        } catch (CurlException $e) {
            $curlErrorCode = (int)$e->getCode();

            if ($curlErrorCode === CURLE_SSL_PEER_CERTIFICATE) { /* SSL certificate problem */
                throw new HttpException(
                    $e->getMessage() . ' (while requesting ' . $this->url . ')' . "\n\n" .
                    'This likely means your curl installation is incomplete and can not verify certificates.' . "\n" .
                    'Please install a cacert.pem (for instance from https://curl.haxx.se/docs/caextract.html) and try again.',
                    $curlErrorCode
                );
            }

            throw new HttpException(
                $e->getMessage() . ' (while requesting ' . $this->url . ')',
                $curlErrorCode
            );
        }

        $httpCode = $this->curl->getHttpCode();

        if ($httpCode >= 400 || in_array($httpCode, [200, 304], true)) {
            return new HttpResponse($httpCode, $result ?: '', $this->etag, $this->parseRateLimitHeaders());
        }

        throw new HttpException(
            sprintf('Unexpected Response Code %d while requesting %s', $httpCode, $this->url->asString()),
            $httpCode
        );
    }

    private function parseRateLimitHeaders(): ?RateLimit {
        $required = ['Limit', 'Remaining', 'Reset'];
        $existing = array_keys($this->rateLimitHeaders);

        if (count(array_intersect($required, $existing)) < 3) {
            return null;
        }

        return new RateLimit(
            (int)$this->rateLimitHeaders['Limit'],
            (int)$this->rateLimitHeaders['Remaining'],
            new DateTimeImmutable('@' . $this->rateLimitHeaders['Reset'])
        );
    }
}
