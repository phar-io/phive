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

use function sprintf;
use PharIo\FileSystem\File;

class FileDownloader {
    /** @var HttpClient */
    private $httpClient;

    /** @var CacheBackend */
    private $cache;

    /** @var null|RateLimit */
    private $rateLimit;

    public function __construct(HttpClient $httpClient, CacheBackend $cache) {
        $this->httpClient = $httpClient;
        $this->cache      = $cache;
    }

    /**
     * @throws DownloadFailedException
     */
    public function download(Url $url): File {
        $this->rateLimit = null;
        $cachedETag      = $this->cache->hasEntry($url) ? $this->cache->getEtag($url) : null;

        try {
            $response = $this->httpClient->get($url, $cachedETag);
        } catch (HttpException $e) {
            throw new DownloadFailedException(
                sprintf(
                    'Unexpected HTTP error: %s (Code: %d)',
                    $e->getMessage(),
                    $e->getCode()
                )
            );
        }

        if ($response->hasRateLimit()) {
            $this->rateLimit = $response->getRateLimit();
        }

        if (!$response->isSuccess()) {
            throw new DownloadFailedException(
                sprintf('Failed to download load %s: HTTP Code %d', $url->asString(), $response->getHttpCode()),
                $response->getHttpCode()
            );
        }

        if ($response->getHttpCode() === 304) {
            return new File($url->getFilename(), $this->cache->getContent($url));
        }

        if ($response->hasETag()) {
            $this->cache->storeEntry($url, $response->getETag(), $response->getBody());
        }

        return new File($url->getFilename(), $response->getBody());
    }

    /** @psalm-assert !null $this->rateLimit */
    public function hasRateLimit(): bool {
        return $this->rateLimit !== null;
    }

    public function getRateLimit(): RateLimit {
        if (!$this->hasRateLimit()) {
            throw new FileDownloaderException('No RateLimit available');
        }

        return $this->rateLimit;
    }
}
