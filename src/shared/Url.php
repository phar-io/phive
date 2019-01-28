<?php declare(strict_types = 1);
namespace PharIo\Phive;

use PharIo\FileSystem\Filename;

class Url {
    /** @var string */
    private $uri;

    /** @var string */
    private $hostname;

    /** @var string */
    private $path;

    public static function isUrl(string $string): bool {
        return \strpos($string, '://') !== false;
    }

    public static function isHttpsUrl($string): bool {
        return \stripos($string, 'https://') === 0;
    }

    public function __construct(string $uri) {
        $components = $this->parseURL($uri);
        $this->ensureHttps($components['scheme'] ?? '');
        $this->uri      = $uri;
        $this->hostname = $components['host'];
        $this->path     = \array_key_exists('path', $components) ? $components['path'] : '/';
    }

    public function __toString(): string {
        return $this->asString();
    }

    public function asString(): string {
        return $this->uri;
    }

    public function getHostname(): string {
        return $this->hostname;
    }

    public function getPath(): string {
        return $this->path;
    }

    public function getFilename(): Filename {
        return new Filename(\basename($this->getPath()));
    }

    public function withParams(array $params): self {
        if (\count($params) === 0) {
            return clone $this;
        }
        $sep = \strpos($this->uri, '?') !== false ? '&' : '?';

        return new self($this->uri . $sep . \http_build_query($params, '', '&', \PHP_QUERY_RFC3986));
    }

    public function equals(self $url): bool {
        return $this->uri === $url->uri;
    }

    /**
     * @param string $protocol
     */
    private function ensureHttps($protocol): void {
        if (\strtolower($protocol) !== 'https') {
            throw new \InvalidArgumentException('Only HTTPS protocol type supported');
        }
    }

    private function parseURL(string $uri): array {
        $components = \parse_url($uri);

        if ($components === false) {
            throw new \InvalidArgumentException('The provided URL cannot be parsed');
        }

        return $components;
    }
}
