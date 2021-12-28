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

interface CacheBackend {
    public function hasEntry(Url $url): bool;

    public function getContent(Url $url): string;

    public function getEtag(Url $url): ETag;

    public function storeEntry(Url $url, ETag $etag, string $content): void;
}
