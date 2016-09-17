<?php
namespace PharIo\Phive;

class HttpResponse {

    /**
     * @var string
     */
    private $responseBody = '';

    /**
     * @var int
     */
    private $httpCode = 0;

    /**
     * @var ETag
     */
    private $etag;

    /**
     * @param integer $httpCode
     * @param string $responseBody
     * @param ETag|null $etag
     */
    public function __construct($httpCode, $responseBody, ETag $etag = null) {
        $this->ensureBodyNotEmpty($httpCode, $responseBody);

        $this->responseBody = $responseBody;
        $this->httpCode = $httpCode;
        $this->etag = $etag;
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
     * @param int $httpCode
     * @param string $body
     *
     * @throws HttpResponseException
     */
    private function ensureBodyNotEmpty($httpCode, $body) {
        if ($httpCode !== 200) {
            return;
        }
        if (empty($body)) {
            throw new HttpResponseException('Response Body must not be empty when HTTP Code is 200');
        }
    }
}
