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

class HttpResponse {
    /** @var string */
    private $responseBody;

    /** @var int */
    private $httpCode;

    /** @var null|ETag */
    private $etag;

    /** @var null|RateLimit */
    private $rateLimit;

    public function __construct(int $httpCode, string $responseBody, ?ETag $etag = null, ?RateLimit $rateLimit = null) {
        $this->responseBody = $responseBody;
        $this->httpCode     = $httpCode;
        $this->etag         = $etag;
        $this->rateLimit    = $rateLimit;
    }

    public function isSuccess(): bool {
        return $this->httpCode < 400;
    }

    public function isNotFound(): bool {
        return $this->httpCode === 404;
    }

    public function getHttpCode(): int {
        return $this->httpCode;
    }

    public function getBody(): string {
        return $this->responseBody;
    }

    /** @psalm-assert !null $this->etag */
    public function hasETag(): bool {
        return $this->etag !== null;
    }

    /**
     * @throws HttpResponseException
     */
    public function getETag(): ETag {
        if (!$this->hasETag()) {
            throw new HttpResponseException('No ETag present in response');
        }

        return $this->etag;
    }

    /** @psalm-assert-if-true RateLimit $this->rateLimit */
    public function hasRateLimit(): bool {
        return $this->rateLimit !== null;
    }

    /**
     * @throws HttpResponseException
     */
    public function getRateLimit(): RateLimit {
        if (!$this->hasRateLimit()) {
            throw new HttpResponseException('No RateLimit present in response');
        }

        return $this->rateLimit;
    }
}
