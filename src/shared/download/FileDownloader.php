<?php
namespace PharIo\Phive;

use PharIo\FileSystem\File;

class FileDownloader {

    /**
     * @var HttpClient
     */
    private $httpClient;

    /**
     * @var CacheBackend
     */
    private $cache;

    /**
     * @param HttpClient   $httpClient
     * @param CacheBackend $cache
     */
    public function __construct(HttpClient $httpClient, CacheBackend $cache) {
        $this->httpClient = $httpClient;
        $this->cache = $cache;
    }

    /**
     * @param Url $url
     *
     * @return File
     * @throws DownloadFailedException
     * @throws \HttpException
     */
    public function download(Url $url) {

        try {
            $cachedETag = $this->cache->hasEntry($url) ? $this->cache->getEtag($url) : null;

            $response = $this->httpClient->get($url, $cachedETag);

            if ($response->getHttpCode() === 304) {
                return new File($url->getFilename(), $this->cache->getContent($url));
            }

            if ($response->hasETag()) {
                $this->cache->storeEntry($url, $response->getETag(), $response->getBody());
            }
            return new File($url->getFilename(), $response->getBody());
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

}
