<?php
namespace PharIo\Phive;

class RetryingHttpClient implements HttpClient {

    /**
     * @var int
     */
    private $maxTries;

    /**
     * @var HttpClient
     */
    private $client;

    /**
     * @var int
     */
    private $triesPerformed = 0;

    private $retryCodes = [
        5 => 'CURLE_COULDNT_RESOLVE_PROXY',
        6 => 'CURLE_COULDNT_RESOLVE_HOST',
        7 => 'CURLE_COULDNT_CONNECT',
        22 => 'CURLE_HTTP_RETURNED_ERROR',
        26 => 'CURLE_READ_ERROR',
        28 => 'CURLE_OPERATION_TIMEDOUT',
        35 => 'CURLE_SSL_CONNECT_ERROR',
        47 => 'CURLE_TOO_MANY_REDIRECTS',
        52 => 'CURLE_GOT_NOTHING',
        56 => 'CURLE_RECV_ERROR',
        67 => 'CURLE_LOGIN_DENIED'
    ];

    public function __construct(HttpClient $client, $maxTries) {
        $this->maxTries = $maxTries;
        $this->client = $client;
    }

    public function get(Url $url, ETag $etag = null) {
        $this->triesPerformed = 0;
        return $this->doTry($url, $etag);
    }

    private function doTry(Url $url, ETag $etag = null) {
        try {
            $this->triesPerformed++;
            return $this->client->get($url, $etag);
        } catch (HttpException $e) {
            if (isset($this->retryCodes[$e->getCode()]) && $this->triesPerformed < $this->maxTries) {
                return $this->doTry($url, $etag);
            }
        }
    }

}
