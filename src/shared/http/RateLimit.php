<?php
namespace PharIo\Phive;

class RateLimit {
    /**
     * @var int
     */
    private $limit;

    /**
     * @var int
     */
    private $remaining;

    /**
     * @var \DateTimeImmutable
     */
    private $reset;

    /**
     * RateLimit constructor.
     *
     * @param int                $limit
     * @param int                $remaining
     * @param \DateTimeImmutable $reset
     */
    public function __construct($limit, $remaining, \DateTimeImmutable $reset) {
        $this->limit = $limit;
        $this->remaining = $remaining;
        $this->reset = $reset;
    }

    /**
     * @return int
     */
    public function getLimit() {
        return $this->limit;
    }

    /**
     * @return int
     */
    public function getRemaining() {
        return $this->remaining;
    }

    /**
     * @return bool
     */
    public function isWithinLimit() {
        return $this->remaining > 0;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getResetTime() {
        return $this->reset;
    }
}
