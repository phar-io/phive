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

class DirectUrlResolver extends AbstractRequestedPharResolver {
    /** @var HttpClient */
    private $httpClient;

    /**
     * DirectUrlResolver constructor.
     */
    public function __construct(HttpClient $httpClient) {
        $this->httpClient = $httpClient;
    }

    public function resolve(RequestedPhar $requestedPhar): SourceRepository {
        if (!$requestedPhar->hasUrl()) {
            return $this->tryNext($requestedPhar);
        }

        $url    = $requestedPhar->getUrl();
        $result = $this->httpClient->head($url);

        if ($result->isNotFound()) {
            return new UrlRepository();
        }

        $sigUrl    = new Url($url->asString() . '.asc');
        $sigResult = $this->httpClient->head($sigUrl);

        if ($sigResult->isNotFound()) {
            return new UrlRepository($url);
        }

        return new UrlRepository($url, $sigUrl);
    }
}
