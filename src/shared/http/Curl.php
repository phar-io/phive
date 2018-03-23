<?php
namespace PharIo\Phive;

/**
 * @codeCoverageIgnore
 */
class Curl {

    /**
     * @var resource
     */
    private $curlHandle;

    /**
     * @param string|null $url
     */
    public function init($url = null) {
        $this->curlHandle = curl_init($url);
    }

    /**
     * @param string $resolveString
     */
    public function setResolve($resolveString) {
        curl_setopt($this->curlHandle, CURLOPT_RESOLVE, $resolveString);
    }

    public function addHttpHeaders(array $headers) {
        curl_setopt($this->curlHandle, CURLOPT_HTTPHEADER, $headers);
    }

    public function disableProgressMeter() {
        curl_setopt($this->curlHandle, CURLOPT_NOPROGRESS, true);
    }

    public function doNotReturnBody() {
        curl_setopt($this->curlHandle, CURLOPT_NOBODY, true);
    }

    /**
     * @param callable $progressFunction
     */
    public function enableProgressMeter($progressFunction) {
        curl_setopt($this->curlHandle, CURLOPT_NOPROGRESS, false);
        curl_setopt($this->curlHandle, CURLOPT_PROGRESSFUNCTION, $progressFunction);
    }

    /**
     * @return int
     */
    public function getHttpCode() {
        return (int)curl_getinfo($this->curlHandle, CURLINFO_HTTP_CODE);
    }

    public function setCertificateFile($filename) {
        curl_setopt($this->curlHandle, CURLOPT_CAINFO, $filename);
    }

    /**
     * @param callable $headerFunction
     */
    public function setHeaderFunction($headerFunction) {
        curl_setopt($this->curlHandle, CURLOPT_HEADERFUNCTION, $headerFunction);
    }

    /**
     * @param array $options
     */
    public function setOptArray(array $options) {
        curl_setopt_array($this->curlHandle, $options);
    }

    /**
     * @param resource $ch
     * @param int $option
     * @param mixed|callable $value
     *
     * @return bool
     */
    public function setOpt($ch, $option, $value) {
        return curl_setopt($ch, $option, $value);
    }

    /**
     * @return mixed
     */
    public function exec() {
        return curl_exec($this->curlHandle);
    }

    /**
     * @param resource $ch
     * @param int|null $opt
     *
     * @return mixed
     */
    public function getInfo($ch, $opt = null) {
        return curl_getinfo($ch, $opt);
    }

    /**
     * @return string
     */
    public function getLastErrorMessage() {
        return curl_error($this->curlHandle);
    }

    /**
     * @return int
     */
    public function getLastErrorNumber() {
        return curl_errno($this->curlHandle);
    }

}
