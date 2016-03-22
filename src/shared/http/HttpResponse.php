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
     * @var string
     */
    private $errorMessage = '';

    /**
     * @param string  $responseBody
     * @param integer $httpCode
     * @param string  $errorMessage
     */
    public function __construct($responseBody, $httpCode, $errorMessage) {
        $this->responseBody = $responseBody;
        $this->httpCode = $httpCode;
        $this->errorMessage = $errorMessage;
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
    public function getErrorMessage() {
        return $this->errorMessage;
    }

    /**
     * @return string
     */
    public function getBody() {
        return $this->responseBody;
    }

}
