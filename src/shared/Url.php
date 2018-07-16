<?php
namespace PharIo\Phive;

use PharIo\FileSystem\Filename;

class Url {

    /**
     * @var string
     */
    private $uri;

    /**
     * @var string
     */
    private $hostname;

    /**
     * @var string
     */
    private $path;

    /**
     * @param string $uri
     */
    public function __construct($uri) {
        $components = $this->parseURL($uri);
        $this->ensureHttps(isset($components['scheme']) ? $components['scheme'] : '');
        $this->uri = $uri;
        $this->hostname = $components['host'];
        $this->path = array_key_exists('path', $components) ? $components['path'] : '/';
    }

    /**
     * @return string
     */
    public function __toString() {
        return $this->uri;
    }

    /**
     * @return string
     */
    public function getHostname() {
        return $this->hostname;
    }

    /**
     * @return string
     */
    public function getPath() {
        return $this->path;
    }

    /**
     * @return Filename
     */
    public function getFilename() {
        return new Filename(basename($this->getPath()));
    }

    /**
     * @param string $protocol
     */
    private function ensureHttps($protocol) {
        if (strtolower($protocol) !== 'https') {
            throw new \InvalidArgumentException('Only HTTPS protocol type supported');
        }
    }

    /**
     * @param string $uri
     *
     * @return array
     */
    private function parseURL($uri) {
        $components = parse_url($uri);
        if ($components === false) {
            throw new \InvalidArgumentException('The provided URL cannot be parsed');
        }

        return $components;
    }

    /**
     * @param array $params
     *
     * @return Url
     */
    public function withParams(array $params) {
        if (count($params) == 0) {
            return clone($this);
        }
        $sep = strpos($this->uri, '?') !== false ? '&' : '?';

        return new self($this->uri . $sep . http_build_query($params, null, '&', PHP_QUERY_RFC3986));
    }

    /**
     * @param $string
     *
     * @return bool
     */
    public static function isUrl($string) {
        return strpos($string, '://') !== false;
    }

    public static function isHttpsUrl($string) {
        return stripos($string, 'https://') === 0;
    }

    /**
     * @param Url $url
     *
     * @return bool
     */
    public function equals(Url $url) {
        return $this->uri === $url->uri;
    }
}
