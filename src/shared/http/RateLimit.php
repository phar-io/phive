<?php declare(strict_types = 1);
namespace PharIo\Phive;

class RateLimit {

    /** @var int */
    private $limit;

    /** @var int */
    private $remaining;

    /** @var \DateTimeImmutable */
    private $reset;

    public function __construct(int $limit, int $remaining, \DateTimeImmutable $reset) {
        $this->limit     = $limit;
        $this->remaining = $remaining;
        $this->reset     = $reset;
    }

    public function getLimit(): int {
        return $this->limit;
    }

    public function getRemaining(): int {
        return $this->remaining;
    }

    public function isWithinLimit(): bool {
        return $this->remaining > 0;
    }

    public function getResetTime(): \DateTimeImmutable {
        return $this->reset;
    }
}
