<?php
namespace PharIo\Phive;

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
     * @param string $uri
     */
    public function __construct($uri) {
        $components = parse_url($uri);
        $this->ensureHttps(isset($components['scheme']) ? $components['scheme'] : '');
        $this->ensureValidHostname(isset($components['host']) ? $components['host'] : '');
        $this->uri = $uri;
        $this->hostname = $components['host'];
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
     * @param string $protocol
     */
    private function ensureHttps($protocol) {
        if (strtolower($protocol) !== 'https') {
            throw new \InvalidArgumentException('Only HTTPS protocol type supported');
        }
    }

    /**
     * @param string $host
     */
    private function ensureValidHostname($host) {
        if ($host === '') {
            throw new \InvalidArgumentException('Provided URL does not seem to contain a hostname');
        }
    }

    /**
     * @param array $params
     *
     * @return Url
     */
    public function withParams(array $params) {
        if (count($params)) {
            return new self($this->uri . '?' . http_build_query($params));
        }
        return $this;
    }

    /**
     * @param $string
     *
     * @return bool
     */
    public static function isUrl($string) {
        return strpos($string, 'https://') !== false;
    }

}
