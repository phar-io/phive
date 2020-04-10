<?php declare(strict_types = 1);
namespace PharIo\Phive;

/**
 * @codeCoverageIgnore
 */
class Curl {
    /** @var resource */
    private $curlHandle;

    public function init(string $url = null): void {
        $this->curlHandle = \curl_init($url);
    }

    public function setResolve(string $resolveString): void {
        \curl_setopt($this->curlHandle, \CURLOPT_RESOLVE, [$resolveString]);
    }

    public function addHttpHeaders(array $headers): void {
        \curl_setopt($this->curlHandle, \CURLOPT_HTTPHEADER, $headers);
    }

    public function disableProgressMeter(): void {
        \curl_setopt($this->curlHandle, \CURLOPT_NOPROGRESS, true);
    }

    public function doNotReturnBody(): void {
        \curl_setopt($this->curlHandle, \CURLOPT_NOBODY, true);
    }

    public function enableProgressMeter(callable $progressFunction): void {
        \curl_setopt($this->curlHandle, \CURLOPT_NOPROGRESS, false);
        \curl_setopt($this->curlHandle, \CURLOPT_PROGRESSFUNCTION, $progressFunction);
    }

    public function getHttpCode(): int {
        return (int)\curl_getinfo($this->curlHandle, \CURLINFO_HTTP_CODE);
    }

    public function setCertificateFile(string $filename): void {
        \curl_setopt($this->curlHandle, \CURLOPT_CAINFO, $filename);
    }

    public function setHeaderFunction(callable $headerFunction): void {
        \curl_setopt($this->curlHandle, \CURLOPT_HEADERFUNCTION, $headerFunction);
    }

    public function setOptArray(array $options): void {
        \curl_setopt_array($this->curlHandle, $options);
    }

    /**
     * @param resource       $ch
     * @param callable|mixed $value
     */
    public function setOpt($ch, int $option, $value): bool {
        return \curl_setopt($ch, $option, $value);
    }

    public function exec(): string {
        $result = \curl_exec($this->curlHandle);
        if ($result === false) {
            throw new CurlException('Request failed');
        }

        return $result;
    }

    public function getLastErrorMessage(): string {
        return \curl_error($this->curlHandle);
    }

    public function getLastErrorNumber(): int {
        return \curl_errno($this->curlHandle);
    }
}
