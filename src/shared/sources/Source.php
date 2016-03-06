<?php
namespace PharIo\Phive;

class Source {

    /**
     * @var string
     */
    private $type;

    /**
     * @var Url
     */
    private $url;

    /**
     * Source constructor.
     *
     * @param string $type
     * @param Url    $url
     */
    public function __construct($type, Url $url) {
        $this->ensureValidSourceType($type);
        $this->type = $type;
        $this->url = $url;
    }

    /**
     * @return string
     */
    public function getType() {
        return $this->type;
    }

    /**
     * @return Url
     */
    public function getUrl() {
        return $this->url;
    }

    /**
     * @param string $type
     */
    private function ensureValidSourceType($type) {
        if (!in_array($type, ['phar.io', 'github'])) {
            throw new \InvalidArgumentException(
                sprintf('Unsupported source repository type "%s"', $type)
            );
        }
    }

}
