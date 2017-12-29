<?php
namespace PharIo\Phive;

/**
 * @codeCoverageIgnore
 */
class Curl implements HttpClient {

    /** @var CurlConfig */
    private $config;

    /** @var HttpProgressHandler */
    private $progressHandler;

    /** @var Url */
    private $url;

    /** @var Etag */
    private $etag;

    /** @var array */
    private $rateLimitHeaders = [];

    /** @var resource */
    private $curlHandle;

    /**
     * @param CurlConfig          $curlConfig
     * @param HttpProgressHandler $progressHandler
     */
    public function __construct(CurlConfig $curlConfig, HttpProgressHandler $progressHandler) {
        $this->config = $curlConfig;
        $this->progressHandler = $progressHandler;
    }

    /**
     * @param Url       $url
     * @param ETag|null $etag
     *
     * @return HttpResponse
     *
     * @throws HttpException
     */
    public function head(Url $url, ETag $etag = null) {
        $this->url = $url;
        $this->etag = $etag;

        $this->setupCurlInstance();
        curl_setopt($this->curlHandle, CURLOPT_NOBODY, true);
        curl_setopt($this->curlHandle, CURLOPT_NOPROGRESS, true);

        return $this->execRequest();
    }

    /**
     * @param Url       $url
     * @param ETag|null $etag
     *
     * @return HttpResponse
     *
     * @throws HttpException
     */
    public function get(Url $url, ETag $etag = null) {
        $this->url = $url;
        $this->etag = $etag;

        $this->progressHandler->start($url);
        $this->setupCurlInstance();
        $result = $this->execRequest();
        $this->progressHandler->finished();

        return $result;
    }

    /**
     * @param resource $ch
     * @param int      $expectedDown
     * @param int      $received
     * @param int      $expectedUp
     * @param int      $sent
     *
     * @return int
     */
    private function handleProgressInfo($ch, $expectedDown, $received, $expectedUp, $sent) {
        $httpCode = (int)curl_getinfo($this->curlHandle, CURLINFO_HTTP_CODE);
        if ($httpCode >= 400) {
            return 0;
        }

        return $this->progressHandler->handleUpdate(
            new HttpProgressUpdate($this->url, $expectedDown, $received, $expectedUp, $sent)
        ) ? 0 : 1;
    }

    /**
     * @param resource $ch
     * @param string   $line
     *
     * @return int
     */
    private function handleHeaderInput($ch, $line) {
        $parts = explode(':', trim($line));

        if (strtolower($parts[0]) === 'etag') {
            $this->etag = new ETag(trim($parts[1]));
        }

        if (strpos($parts[0], 'X-RateLimit-') !== false) {
            $this->rateLimitHeaders[substr($parts[0],12)] = trim($parts[1]);
        }

        return mb_strlen($line);
    }

    private function setupCurlInstance() {
        $ch = curl_init($this->url);

        curl_setopt_array($ch, $this->config->asCurlOptArray());
        curl_setopt($ch, CURLOPT_NOPROGRESS, false);
        curl_setopt($ch, CURLOPT_HEADERFUNCTION, [$this, 'handleHeaderInput']);
        curl_setopt($ch, CURLOPT_PROGRESSFUNCTION, [$this, 'handleProgressInfo']);

        $headers = [];
        if ($this->etag !== null) {
            $headers[] = 'If-None-Match: ' . $this->etag->asString();
        }

        $hostname = $this->url->getHostname();
        if ($this->config->hasLocalSslCertificate($hostname)) {
            curl_setopt($ch, CURLOPT_CAINFO, $this->config->getLocalSslCertificate($hostname)->getCertificateFile());
        }

        if ($this->config->hasAuthenticationToken($hostname)) {
            $headers[] = sprintf('Authorization: token %s', $this->config->getAuthenticationToken($hostname));
        }

        if (count($headers) > 0) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }

        $this->curlHandle = $ch;
    }

    /**
     * @return HttpResponse
     *
     * @throws HttpException
     */
    private function execRequest() {
        $this->rateLimitHeaders = [];

        $result = curl_exec($this->curlHandle);

        $httpCode = (int)curl_getinfo($this->curlHandle, CURLINFO_HTTP_CODE);

        if ($httpCode >= 400 || in_array($httpCode, [200, 304], true)) {
            return new HttpResponse($httpCode, $result, $this->etag, $this->parseRateLimitHeaders());
        }

        if ($httpCode > 0) {
            throw new HttpException(
                sprintf('Unexpected Response Code %d while requesting %s', $httpCode, $this->url),
                $httpCode
            );
        }

        throw new HttpException(
            curl_error($this->curlHandle) . ' (while requesting ' . $this->url . ')',
            curl_errno($this->curlHandle)
        );

    }

    private function parseRateLimitHeaders() {
        $required = ['Limit', 'Remaining', 'Reset'];
        $exisiting = array_keys($this->rateLimitHeaders);
        if (count(array_intersect($required, $exisiting)) < 3) {
            return null;
        }

        return new RateLimit(
            (int) $this->rateLimitHeaders['Limit'],
            (int) $this->rateLimitHeaders['Remaining'],
            new \DateTimeImmutable('@' . $this->rateLimitHeaders['Reset'])
        );
    }

}
