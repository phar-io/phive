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

class UrlRepository implements SourceRepository {
    /** @var null|Url */
    private $url;

    /** @var null|Url */
    private $sigUrl;

    /**
     * UrlRepository constructor.
     */
    public function __construct(?Url $url = null, ?Url $sigUrl = null) {
        $this->url    = $url;
        $this->sigUrl = $sigUrl;
    }

    public function getReleasesByRequestedPhar(RequestedPhar $requestedPhar): ReleaseCollection {
        $releases = new ReleaseCollection();

        if ($this->url === null) {
            return $releases;
        }

        if ($requestedPhar->getUrl()->equals($this->url)) {
            $releases->add(
                new SupportedRelease(
                    $requestedPhar->getUrl()->getPharName(),
                    $requestedPhar->getUrl()->getPharVersion(),
                    $requestedPhar->getUrl(),
                    $this->sigUrl
                )
            );
        }

        return $releases;
    }
}
