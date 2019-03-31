<?php declare(strict_types = 1);
namespace PharIo\Phive;

class CurlHttpClient implements HttpClient {

    /** @var CurlConfig */
    private $config;

    /** @var HttpProgressHandler */
    private $progressHandler;

    /** @var Url */
    private $url;

    /** @var ETag */
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
    public function head(Url $url, ETag $etag = null): HttpResponse {
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
    public function get(Url $url, ETag $etag = null): HttpResponse {
        $this->url  = $url;
        $this->etag = $etag;

        $this->progressHandler->start($url);
        $this->setupCurlInstance();
        $result = $this->execRequest();
        $this->progressHandler->finished();

        return $result;
    }

    public function handleProgressInfo($ch, int $expectedDown, int $received, int $expectedUp, int $sent): int {
        $httpCode = $this->curl->getHttpCode();

        if ($httpCode >= 400) {
            return 0;
        }

        return $this->progressHandler->handleUpdate(
            new HttpProgressUpdate($this->url, $expectedDown, $received, $expectedUp, $sent)
        ) ? 0 : 1;
    }

    public function handleHeaderInput($ch, string $line): int {
        $parts = \explode(':', \trim($line));

        if (\strtolower($parts[0]) === 'etag') {
            $this->etag = new ETag(\trim($parts[1]));
        }

        if (\strpos($parts[0], 'X-RateLimit-') !== false) {
            $this->rateLimitHeaders[\substr($parts[0], 12)] = \trim($parts[1]);
        }

        return \mb_strlen($line);
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

        if ($this->config->hasAuthenticationToken($hostname)) {
            $headers[] = \sprintf('Authorization: token %s', $this->config->getAuthenticationToken($hostname));
        }

        if (\count($headers) > 0) {
            $this->curl->addHttpHeaders($headers);
        }
    }

    /**
     * @throws HttpException
     */
    private function execRequest(): HttpResponse {
        $this->rateLimitHeaders = [];

        $result = $this->curl->exec();

        $httpCode = $this->curl->getHttpCode();

        if ($httpCode >= 400 || \in_array($httpCode, [200, 304], true)) {
            return new HttpResponse($httpCode, $result ?? '', $this->etag, $this->parseRateLimitHeaders());
        }

        if ($httpCode > 0) {
            throw new HttpException(
                \sprintf('Unexpected Response Code %d while requesting %s', $httpCode, $this->url),
                $httpCode
            );
        }

        if ($this->curl->getLastErrorNumber() === 60) { /* SSL certificate problem */
            throw new HttpException(
                $this->curl->getLastErrorMessage() . ' (while requesting ' . $this->url . ')' . "\n\n" .
                'This likely means your curl installation is incomplete and can not verify certificates.' . "\n" .
                'Please install a cacert.pem (for instance from https://curl.haxx.se/docs/caextract.html) and try again.',
                $this->curl->getLastErrorNumber()
            );
        }

        throw new HttpException(
            $this->curl->getLastErrorMessage() . ' (while requesting ' . $this->url . ')',
            $this->curl->getLastErrorNumber()
        );
    }

    private function parseRateLimitHeaders(): ?RateLimit {
        $required  = ['Limit', 'Remaining', 'Reset'];
        $exisiting = \array_keys($this->rateLimitHeaders);

        if (\count(\array_intersect($required, $exisiting)) < 3) {
            return null;
        }

        return new RateLimit(
            (int)$this->rateLimitHeaders['Limit'],
            (int)$this->rateLimitHeaders['Remaining'],
            new \DateTimeImmutable('@' . $this->rateLimitHeaders['Reset'])
        );
    }
}
