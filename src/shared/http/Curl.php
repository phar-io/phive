<?php
namespace PharIo\Phive;

/**
 * @codeCoverageIgnore
 */
class Curl implements HttpClient {

    /**
     * @var CurlConfig
     */
    private $config;

    /**
     * @var HttpProgressHandler
     */
    private $progressHandler;

    /**
     * @var Url
     */
    private $url;

    /**
     * @var CacheBackend
     */
    private $cache;

    /**
     * @var ETag
     */
    private $etag;

    /**
     * @param CacheBackend $cache
     * @param CurlConfig $curlConfig
     * @param HttpProgressHandler $progressHandler
     */
    public function __construct(
        CacheBackend $cache, CurlConfig $curlConfig, HttpProgressHandler $progressHandler
    ) {
        $this->cache = $cache;
        $this->config = $curlConfig;
        $this->progressHandler = $progressHandler;
    }

    /**
     * @param Url $url
     * @param array $params
     *
     * @return HttpResponse
     *
     */
    public function get(Url $url, array $params = []) {
        $this->url = $url->withParams($params);
        $ch = $this->getCurlInstance($this->url);

        curl_setopt($ch, CURLOPT_NOPROGRESS, false);
        curl_setopt($ch, CURLOPT_PROGRESSFUNCTION, [$this, 'handleProgressInfo']);

        $result = $this->exec($ch);

        $this->progressHandler->finished();

        if ($result->getHttpCode() === 200 && $this->etag instanceof ETag) {
            $this->cache->storeEntry($this->url, $this->etag, $result->getBody());
        }

        if ($result->getHttpCode() === 304) { // not modified
            return new HttpResponse(
                $this->cache->getContent($this->url),
                200,
                ''
            );
        }

        return $result;
    }

    /**
     * @param Url   $url
     * @param array $params
     *
     * @return HttpResponse
     * @throws HttpException
     */
    public function head(Url $url, array $params = []) {
        $this->url = $url->withParams($params);
        $ch = $this->getCurlInstance($this->url);
        $result = $this->exec($ch);
        if ($result->getHttpCode() === 304) { // not modified
            return new HttpResponse('', 200, '');
        }
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
        return mb_strlen($line);
    }

    /**
     * @param Url $url
     *
     * @return resource
     */
    private function getCurlInstance(Url $url) {
        $ch = curl_init($url);
        curl_setopt_array($ch, $this->config->asCurlOptArray());

        if ($this->cache->hasEntry($url)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'If-None-Match: ' . $this->cache->getEtag($url)->asString()
            ]);
        }
        curl_setopt($ch, CURLOPT_HEADERFUNCTION, [$this, 'handleHeaderInput']);

        $hostname = $url->getHostname();
        if ($this->config->hasLocalSslCertificate($hostname)) {
            curl_setopt($ch, CURLOPT_CAINFO, $this->config->getLocalSslCertificate($hostname)->getCertificateFile());
        }

        return $ch;
    }

    /**
     * @param string              $method
     * @param Url                 $url
     * @param array               $params
     *
     * @param HttpProgressHandler $progressHandler
     *
     * @return HttpResponse
     *
     * @throws HttpException
     */
    private function exec($ch) {
        try {
            $result = curl_exec($ch);
            if (curl_errno($ch) !== 0) {
                throw new HttpException(
                    curl_error($ch) . ' (while requesting ' . $this->url . ')',
                    curl_errno($ch)
                );
            }
            return new HttpResponse(
                $result,
                curl_getinfo($ch, CURLINFO_HTTP_CODE),
                curl_error($ch)
            );
        } catch (CurlException $e) {
            throw new HttpException(
                '[CurlException] ' . $e->getMessage() . ' (while requesting ' . $this->url . ')',
                $e->getCode(),
                $e);
        }
    }

}
