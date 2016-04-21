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
     * @param CurlConfig $curlConfig
     */
    public function __construct(CurlConfig $curlConfig) {
        $this->config = $curlConfig;
    }

    /**
     * @param Url                 $url
     * @param array               $params
     *
     * @param HttpProgressHandler $progressHandler
     *
     * @return HttpResponse
     *
     * @throws HttpException
     */
    public function get(Url $url, array $params = [], HttpProgressHandler $progressHandler = null) {
        return $this->exec('GET', $url, $params, $progressHandler);
    }

    /**
     * @param Url   $url
     * @param array $params
     *
     * @return HttpResponse
     * @throws HttpException
     */
    public function head(Url $url, array $params = []) {
        return $this->exec('HEAD', $url, $params);
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
        if (!$this->progressHandler) {
            return 0;
        }

        return $this->progressHandler->handleUpdate(
            new HttpProgressUpdate($this->url, $expectedDown, $received, $expectedUp, $sent)
        ) ? 0 : 1;
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
    private function exec($method, Url $url, array $params = [], HttpProgressHandler $progressHandler = null) {
        try {
            $this->progressHandler = $progressHandler;
            $this->url = $url;

            $ch = curl_init($url . '?' . http_build_query($params));
            curl_setopt_array($ch, $this->config->asCurlOptArray());
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
            curl_setopt($ch, CURLOPT_NOPROGRESS, false);
            curl_setopt($ch, CURLOPT_PROGRESSFUNCTION, [$this, 'handleProgressInfo']);

            $hostname = $url->getHostname();
            if ($this->config->hasLocalSslCertificate($hostname)) {
                curl_setopt($ch, CURLOPT_CAINFO, $this->config->getLocalSslCertificate($hostname)->getCertificateFile());
            }

            return new HttpResponse(curl_exec($ch), curl_getinfo($ch, CURLINFO_HTTP_CODE), curl_error($ch));
        } catch (CurlException $e) {
            throw new HttpException($e->getMessage(), $e->getCode(), $e);
        }
    }

}
