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

use function sleep;
use function sprintf;

class RetryingHttpClient implements HttpClient {
    private const retryCodes = [
        5  => 'CURLE_COULDNT_RESOLVE_PROXY',
        6  => 'CURLE_COULDNT_RESOLVE_HOST',
        7  => 'CURLE_COULDNT_CONNECT',
        22 => 'CURLE_HTTP_RETURNED_ERROR',
        26 => 'CURLE_READ_ERROR',
        28 => 'CURLE_OPERATION_TIMEDOUT',
        35 => 'CURLE_SSL_CONNECT_ERROR',
        47 => 'CURLE_TOO_MANY_REDIRECTS',
        52 => 'CURLE_GOT_NOTHING',
        56 => 'CURLE_RECV_ERROR',
        67 => 'CURLE_LOGIN_DENIED'
    ];

    /** @var int */
    private $maxTries;

    /** @var HttpClient */
    private $client;

    /** @var int */
    private $triesPerformed = 0;

    /** @var Cli\Output */
    private $output;

    public function __construct(Cli\Output $output, HttpClient $client, int $maxTries) {
        $this->maxTries = $maxTries;
        $this->client   = $client;
        $this->output   = $output;
    }

    public function head(Url $url, ?ETag $etag = null): HttpResponse {
        $this->triesPerformed = 0;

        return $this->doTry('head', $url, $etag);
    }

    public function get(Url $url, ?ETag $etag = null): HttpResponse {
        $this->triesPerformed = 0;

        return $this->doTry('get', $url, $etag);
    }

    private function doTry(string $method, Url $url, ?ETag $etag = null): HttpResponse {
        try {
            $this->triesPerformed++;

            return $this->client->{$method}($url, $etag);
        } catch (HttpException $e) {
            if ($this->triesPerformed < $this->maxTries && isset(self::retryCodes[$e->getCode()])) {
                $this->output->writeInfo(
                    sprintf('HTTP Request failed (%s) - retrying in 2 seconds', $e->getCode())
                );
                sleep(2);

                return $this->doTry($method, $url, $etag);
            }

            throw $e;
        }
    }
}
