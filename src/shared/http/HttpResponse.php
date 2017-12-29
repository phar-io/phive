<?php
namespace PharIo\Phive;

class HttpResponse {

    /**
     * @var string
     */
    private $responseBody;

    /**
     * @var int
     */
    private $httpCode;

    /**
     * @var ETag|null
     */
    private $etag;

    /**
     * @var RateLimit
     */
    private $rateLimit;

    /**
     * @param integer   $httpCode
     * @param string    $responseBody
     * @param ETag|null $etag
     */
    public function __construct($httpCode, $responseBody, ETag $etag = null, RateLimit $rateLimit = null) {
        $this->responseBody = $responseBody;
        $this->httpCode = $httpCode;
        $this->etag = $etag;
        $this->rateLimit = $rateLimit;
    }

    public function isSuccess() {
        return $this->httpCode < 400;
    }

    public function isNotFound() {
        return $this->httpCode === 404;
    }

    /**
     * @return int
     */
    public function getHttpCode() {
        return $this->httpCode;
    }

    /**
     * @return string
     */
    public function getBody() {
        return $this->responseBody;
    }

    /**
     * @return bool
     */
    public function hasETag() {
        return $this->etag !== null;
    }

    /**
     * @return ETag
     * @throws HttpResponseException
     */
    public function getETag() {
        if (!$this->hasETag()) {
            throw new HttpResponseException('No ETag present in response');
        }

        return $this->etag;
    }

    /**
     * @return bool
     */
    public function hasRateLimit() {
        return $this->rateLimit !== null;
    }

    /**
     * @return RateLimit
     *
     * @throws HttpResponseException
     */
    public function getRateLimit() {
        if (!$this->hasRateLimit()) {
            throw new HttpResponseException('No RateLimit present in response');
        }
        return $this->rateLimit;
    }

}
