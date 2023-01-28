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

use function file_put_contents;
use function sha1;
use function str_replace;
use PharIo\FileSystem\Directory;

class FileStorageCacheBackend implements CacheBackend {
    /** @var Directory */
    private $basedir;

    public function __construct(Directory $basedir) {
        $basedir->ensureExists(0700);
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
        file_put_contents($dir->file('content')->asString(), $content);
        file_put_contents($dir->file('etag')->asString(), $etag->asString());
    }

    private function translateUrlToName(Url $url): string {
        return str_replace('/', '_', $url->getPath()) . '-' . sha1($url->asString());
    }

    private function getStorageDirectory(Url $url): Directory {
        $dir = $this->basedir->child($url->getHostname())->child(
            $this->translateUrlToName($url)
        );
        $dir->ensureExists(0700);

        return $dir;
    }
}
