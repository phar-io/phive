<?php
namespace PharIo\Phive;

class NullCacheBackend implements CacheBackend {

    public function hasEntry(Url $url) {
        return false;
    }

    public function getContent(Url $url) {
        throw new NullCacheBackendException('Not implemented');
    }

    public function getEtag(Url $url) {
        throw new NullCacheBackendException('Not implemented');
    }

    public function storeEntry(Url $url, ETag $etag, $content) {
    }

}
