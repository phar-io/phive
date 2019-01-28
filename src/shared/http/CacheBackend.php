<?php declare(strict_types = 1);
namespace PharIo\Phive;

interface CacheBackend {
    public function hasEntry(Url $url): bool;

    public function getContent(Url $url): string;

    public function getEtag(Url $url): ETag;

    public function storeEntry(Url $url, ETag $etag, string $content);
}
