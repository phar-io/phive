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
use function curl_setopt;
use function curl_setopt_array;
use CurlHandle;

/**
 * @codeCoverageIgnore
 */
class Curl {
    /** @var CurlHandle */
    private $curlHandle;

    public function init(string $url): void {
        $this->curlHandle = curl_init($url);
    }

    public function setResolve(string $resolveString): void {
        curl_setopt($this->curlHandle, CURLOPT_RESOLVE, [$resolveString]);
    }

    public function addHttpHeaders(array $headers): void {
        curl_setopt($this->curlHandle, CURLOPT_HTTPHEADER, $headers);
    }

    public function disableProgressMeter(): void {
        curl_setopt($this->curlHandle, CURLOPT_NOPROGRESS, true);
    }

    public function doNotReturnBody(): void {
        curl_setopt($this->curlHandle, CURLOPT_NOBODY, true);
    }

    public function enableProgressMeter(callable $progressFunction): void {
        curl_setopt($this->curlHandle, CURLOPT_NOPROGRESS, false);
        curl_setopt($this->curlHandle, CURLOPT_PROGRESSFUNCTION, $progressFunction);
    }

    public function getHttpCode(): int {
        return (int)curl_getinfo($this->curlHandle, CURLINFO_HTTP_CODE);
    }

    public function setCertificateFile(string $filename): void {
        curl_setopt($this->curlHandle, CURLOPT_CAINFO, $filename);
    }

    public function setHeaderFunction(callable $headerFunction): void {
        curl_setopt($this->curlHandle, CURLOPT_HEADERFUNCTION, $headerFunction);
    }

    public function setOptArray(array $options): void {
        curl_setopt_array($this->curlHandle, $options);
    }

    /**
     * @throws CurlException
     * @psalm-suppress InvalidReturnType
     */
    public function exec(): string {
        /** @var false|string $result */
        $result = curl_exec($this->curlHandle);

        if ($result === false) {
            throw new CurlException('Request failed');
        }

        return $result;
    }

    public function getLastErrorMessage(): string {
        return curl_error($this->curlHandle);
    }

    public function getLastErrorNumber(): int {
        return curl_errno($this->curlHandle);
    }
}
