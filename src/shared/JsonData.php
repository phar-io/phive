<?php declare(strict_types = 1);
namespace PharIo\Phive;

class JsonData {
    /** @var string */
    private $raw;

    /** @var array|\stdClass */
    private $parsed;

    /**
     * @param string $raw
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($raw) {
        $this->raw = $raw;
        $parsed    = \json_decode($raw, false, 512, \JSON_BIGINT_AS_STRING);

        if (\json_last_error() !== \JSON_ERROR_NONE) {
            throw new \InvalidArgumentException(\json_last_error_msg(), \json_last_error());
        }

        if (!$parsed instanceof \stdClass && !\is_array($parsed)) {
            throw new \InvalidArgumentException('Given JSON string does not parse into object or array');
        }
        $this->parsed = $parsed;
    }

    public function getRaw(): string {
        return $this->raw;
    }

    /**
     * @return array<\StdClass>
     */
    public function getParsed() {
        return $this->parsed;
    }

    public function hasFragment(string $fragmentSpecification): bool {
        try {
            $this->getFragment($fragmentSpecification);

            return true;
        } catch (\InvalidArgumentException $e) {
            return false;
        }
    }

    /**
     * @param string $fragmentSpecification
     *
     * @return array<string, string>
     */
    public function getFragment($fragmentSpecification) {
        /** @var \StdClass $data */
        $data = $this->parsed;

        foreach (\explode('.', $fragmentSpecification) as $key) {
            if (!\property_exists($data, $key)) {
                throw new \InvalidArgumentException(
                    \sprintf('Fragment %s of %s not found', $key, $fragmentSpecification)
                );
            }
            $data = $data->{$key};
        }

        return $data;
    }
}
