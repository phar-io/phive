<?php
namespace PharIo\Phive;

interface CacheBackend {

    /**
     * @param Url $url
     *
     * @return bool
     */
    public function hasEntry(Url $url);

    /**
     * @param Url $url
     *
     * @return string
     */
    public function getContent(Url $url);

    /**
     * @param Url $url
     *
     * @return ETag
     */
    public function getEtag(Url $url);

    /**
     * @param Url    $url
     * @param ETag   $etag
     * @param string $content
     */
    public function storeEntry(Url $url, ETag $etag, $content);
}
