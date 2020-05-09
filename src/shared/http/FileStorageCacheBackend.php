<?php declare(strict_types = 1);
namespace PharIo\Phive;

use PharIo\FileSystem\Directory;

class FileStorageCacheBackend implements CacheBackend {

    /** @var Directory */
    private $basedir;

    public function __construct(Directory $basedir) {
        $this->basedir = $basedir;
    }

    public function hasEntry(Url $url): bool {
        if (!$this->basedir->hasChild($url->getHostname())) {
            return false;
        }

        return $this->basedir->child($url->getHostname())->hasChild(
            $this->translateUrlToName($url)
        );
    }

    public function getContent(Url $url): string {
        return $this->getStorageDirectory($url)->file('content')->read()->getContent();
    }

    public function getEtag(Url $url): ETag {
        return new ETag(
            $this->getStorageDirectory($url)->file('etag')->read()->getContent()
        );
    }

    public function storeEntry(Url $url, ETag $etag, string $content): void {
        $dir = $this->getStorageDirectory($url);
        \file_put_contents($dir->file('content')->asString(), $content);
        \file_put_contents($dir->file('etag')->asString(), $etag->asString());
    }

    private function translateUrlToName(Url $url): string {
        return \str_replace('/', '_', $url->getPath()) . '-' . \sha1((string)$url);
    }

    private function getStorageDirectory(Url $url): Directory {
        return $this->basedir->child($url->getHostname())->child(
            $this->translateUrlToName($url)
        );
    }
}
