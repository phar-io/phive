<?php
namespace PharIo\Phive;

class FileDownloader {

    /**
     * @var HttpClient
     */
    private $httpClient;

    /**
     * @var HttpProgressHandler
     */
    private $httpProgressHandler;

    /**
     * @param HttpClient $httpClient
     * @param HttpProgressHandler $httpProgressHandler
     */
    public function __construct(HttpClient $httpClient, HttpProgressHandler $httpProgressHandler) {
        $this->httpClient = $httpClient;
        $this->httpProgressHandler = $httpProgressHandler;
    }

    /**
     * @param Url $url
     *
     * @return File
     * @throws DownloadFailedException
     */
    public function download(Url $url) {

        try {
            $response = $this->httpClient->get($url, [], $this->httpProgressHandler);

            if ($response->getHttpCode() !== 200) {
                throw new DownloadFailedException(
                    sprintf(
                        'Download failed (HTTP status code %s) %s',
                        $response->getHttpCode(),
                        $response->getErrorMessage()
                    )
                );
            }
            if (empty($response->getBody())) {
                throw new DownloadFailedException('Download failed - response is empty');
            }
            return new File($this->getFilename($url), $response->getBody());
        } catch (HttpException $e) {
            throw new DownloadFailedException(
                sprintf(
                    'Download failed (Error code %s) %s',
                    $e->getCode(),
                    $e->getMessage()
                )
            );
        }
    }

    /**
     * @param Url $url
     *
     * @return Filename
     */
    private function getFilename(Url $url) {
        return new Filename(pathinfo($url, PATHINFO_BASENAME));
    }

}
