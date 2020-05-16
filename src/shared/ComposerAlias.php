<?php declare(strict_types = 1);
namespace PharIo\Phive;

class ComposerAlias {
    /** @var string */
    private $vendor;

    /** @var string */
    private $name;

    public function __construct(string $alias) {
        $this->ensureValidFormat($alias);
        [$this->vendor, $this->name] = \explode('/', $alias);
    }

    public function asString(): string {
        return $this->vendor . '/' . $this->name;
    }

    public function getVendor(): string {
        return $this->vendor;
    }

    public function getName(): string {
        return $this->name;
    }

    /**
     * @param string $alias
     *
     * @throws \InvalidArgumentException
     */
    private function ensureValidFormat($alias): void {
        $check = \strpos($alias, '/');

        if ($check === false || $check === 0) {
            throw new \InvalidArgumentException(
                \sprintf('Invalid composer alias, must be of format "vendor/name", "%s" given', $alias)
            );
        }
    }
}
