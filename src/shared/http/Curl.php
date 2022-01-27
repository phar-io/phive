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

use const CURLINFO_HTTP_CODE;
use const CURLOPT_CAINFO;
use const CURLOPT_HEADERFUNCTION;
use const CURLOPT_HTTPHEADER;
use const CURLOPT_NOBODY;
use const CURLOPT_NOPROGRESS;
use const CURLOPT_PROGRESSFUNCTION;
use const CURLOPT_RESOLVE;
use function curl_errno;
use function curl_error;
use function curl_exec;
use function curl_getinfo;
use function curl_init;
use function curl_setopt_array;

/**
 * @codeCoverageIgnore
 */
class Curl {
    /** @var string */
    private $url;

    /** @var array<int, mixed> */
    private $options = [];

    /** @var int */
    private $httpCode = 0;

    public function init(string $url): void {
        $this->url      = $url;
        $this->options  = [];
        $this->httpCode = 0;
    }

    public function setResolve(string $resolveString): void {
        $this->options[CURLOPT_RESOLVE] = [$resolveString];
    }

    public function addHttpHeaders(array $headers): void {
        $this->options[CURLOPT_HTTPHEADER] = $headers;
    }

    public function disableProgressMeter(): void {
        $this->options[CURLOPT_NOPROGRESS] = true;
    }

    public function doNotReturnBody(): void {
        $this->options[CURLOPT_NOBODY] = true;
    }

    public function enableProgressMeter(callable $progressFunction): void {
        $this->options[CURLOPT_NOPROGRESS]       = false;
        $this->options[CURLOPT_PROGRESSFUNCTION] = $progressFunction;
    }

    public function setCertificateFile(string $filename): void {
        $this->options[CURLOPT_CAINFO] = $filename;
    }

    public function setHeaderFunction(callable $headerFunction): void {
        $this->options[CURLOPT_HEADERFUNCTION] = $headerFunction;
    }

    public function setOptArray(array $options): void {
        $this->options = $options + $this->options;
    }

    /**
     * @throws CurlException
     */
    public function exec(): string {
        $ch = curl_init($this->url);
        assert($ch !== false);
        curl_setopt_array($ch, $this->options);
        $result         = curl_exec($ch);
        $this->httpCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($result === false) {
            throw new CurlException(curl_error($ch), curl_errno($ch));
        }

        return (string)$result;
    }

    public function getHttpCode(): int {
        return $this->httpCode;
    }
}
