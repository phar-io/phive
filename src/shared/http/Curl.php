<?php
namespace PharIo\Phive;

class Curl implements HttpClient {

    /**
     * @var CurlConfig
     */
    private $config;

    /**
     * @var callable
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
     * @param Url   $url
     * @param array $params
     *
     * @return HttpResponse
     */
    public function get(Url $url, array $params = [], HttpProgressHandler $progressHandler = null) {
        $this->progressHandler = $progressHandler;
        $this->url = $url;

        $ch = curl_init($url . '?' . http_build_query($params));
        curl_setopt_array($ch, $this->config->asCurlOptArray());
        curl_setopt($ch, CURLOPT_NOPROGRESS, false);
        curl_setopt($ch, CURLOPT_PROGRESSFUNCTION, [$this, 'handleProgressInfo']);

        $hostname = parse_url((string)$url, PHP_URL_HOST);
        if ($this->config->hasLocalSslCertificate($hostname)) {
            curl_setopt($ch, CURLOPT_CAINFO, $this->config->getLocalSslCertificate($hostname));
        }

        return new HttpResponse(curl_exec($ch), curl_getinfo($ch, CURLINFO_HTTP_CODE), curl_error($ch));
    }

    /**
     * @param resource $ch
     * @param int $expectedDown
     * @param int $received
     * @param int $expectedUp
     * @param int $sent
     *
     * @return int
     */
    private function handleProgressInfo($ch, $expectedDown, $received, $expectedUp, $sent) {
        if (!$this->progressHandler) {
            return 0;
        }

        $this->progressHandler->handleUpdate(
            new HttpProgressUpdate($this->url, $expectedDown, $received, $expectedUp, $sent)
        ) ? 0 : 1;
    }
}
