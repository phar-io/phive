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
     * @var ETag
     */
    private $etag;

    /**
     * @param CurlConfig $curlConfig
     * @param HttpProgressHandler $progressHandler
     */
    public function __construct(CurlConfig $curlConfig, HttpProgressHandler $progressHandler) {
        $this->config = $curlConfig;
        $this->progressHandler = $progressHandler;
    }

    /**
     * @param Url $url
     * @param ETag $etag
     *
     * @return HttpResponse
     */
    public function get(Url $url, ETag $etag = null) {
        $this->url = $url;
        $ch = $this->getCurlInstance($this->url, $etag);

        $result = $this->exec($ch);

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
     * @param ETag $etag
     *
     * @return resource
     */
    private function getCurlInstance(Url $url, ETag $etag = null) {
        $ch = curl_init($url);
        curl_setopt_array($ch, $this->config->asCurlOptArray());

        curl_setopt($ch, CURLOPT_NOPROGRESS, false);
        curl_setopt($ch, CURLOPT_PROGRESSFUNCTION, [$this, 'handleProgressInfo']);
        curl_setopt($ch, CURLOPT_HEADERFUNCTION, [$this, 'handleHeaderInput']);

        if ($etag !== null) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'If-None-Match: ' . $etag->asString()
            ]);
        }

        $hostname = $url->getHostname();
        if ($this->config->hasLocalSslCertificate($hostname)) {
            curl_setopt($ch, CURLOPT_CAINFO, $this->config->getLocalSslCertificate($hostname)->getCertificateFile());
        }

        return $ch;
    }

    /**
     * @param $ch
     *
     * @return HttpResponse
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

            $httpCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if (!in_array($httpCode, [200, 304])) {
                throw new HttpException(
                    sprintf('Unexpected Response Code %d while requesting %s', $httpCode, $this->url),
                    $httpCode
                );
            }

            return new HttpResponse($httpCode, $result, $this->etag);
        } catch (CurlException $e) {
            throw new HttpException(
                '[CurlException] ' . $e->getMessage() . ' (while requesting ' . $this->url . ')',
                $e->getCode(),
                $e
            );
        } catch (HttpResponseException $e) {
            throw new HttpException(
                '[ResponseException] ' . $e->getMessage() . ' (while requesting ' . $this->url . ')',
                $e->getCode(),
                $e
            );
        }
    }

}
