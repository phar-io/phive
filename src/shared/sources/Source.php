<?php declare(strict_types = 1);
namespace PharIo\Phive;

class Source {

    /** @var string */
    private $type;

    /** @var Url */
    private $url;

    public function __construct(string $type, Url $url) {
        $this->ensureValidSourceType($type);
        $this->type = $type;
        $this->url  = $url;
    }

    public function getType(): string {
        return $this->type;
    }

    public function getUrl(): Url {
        return $this->url;
    }

    private function ensureValidSourceType(string $type): void {
        if (!\in_array($type, ['phar.io', 'github'])) {
            throw new \InvalidArgumentException(
                \sprintf('Unsupported source repository type "%s"', $type)
            );
        }
    }
}
