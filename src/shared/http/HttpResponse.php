<?php declare(strict_types = 1);
namespace PharIo\Phive;

class HttpResponse {

    /** @var string */
    private $responseBody;

    /** @var int */
    private $httpCode;

    /** @var null|ETag */
    private $etag;

    /** @var RateLimit */
    private $rateLimit;

    public function __construct(int $httpCode, string $responseBody, ETag $etag = null, RateLimit $rateLimit = null) {
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
