<?php
namespace PharIo\Phive;

use PharIo\FileSystem\Directory;

class FileStorageCacheBackend implements CacheBackend {

    /**
     * @var Directory
     */
    private $basedir;

    /**
     * FileStorageCacheBackend constructor.
     *
     * @param Directory $basedir
     */
    public function __construct(Directory $basedir) {
        $this->basedir = $basedir;
    }

    /**
     * @param Url $url
     *
     * @return bool
     */
    public function hasEntry(Url $url) {
        if (!$this->basedir->hasChild($url->getHostname())) {
            return false;
        }
        return $this->basedir->child($url->getHostname())->hasChild(
            $this->translateUrlToName($url)
        );
    }

    /**
     * @param Url $url
     *
     * @return string
     */
    public function getContent(Url $url) {
        return $this->getStorageDirectory($url)->file('content')->read()->getContent();
    }

    /**
     * @param Url $url
     *
     * @return ETag
     */
    public function getEtag(Url $url) {
        return new ETag(
            $this->getStorageDirectory($url)->file('etag')->read()->getContent()
        );
    }

    /**
     * @param Url    $url
     * @param ETag   $etag
     * @param string $content
     */
    public function storeEntry(Url $url, ETag $etag, $content) {
        $dir = $this->getStorageDirectory($url);
        file_put_contents($dir->file('content')->asString(), $content);
        file_put_contents($dir->file('etag')->asString(), $etag->asString());
    }

    /**
     * @param Url $url
     *
     * @return string
     */
    private function translateUrlToName(Url $url) {
        return str_replace('/', '_', $url->getPath()) . '-' . sha1((string)$url);
    }

    /**
     * @param Url $url
     *
     * @return Directory
     */
    private function getStorageDirectory(Url $url) {
        return $this->basedir->child($url->getHostname())->child(
            $this->translateUrlToName($url)
        );
    }
}
