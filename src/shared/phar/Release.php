<?php declare(strict_types = 1);
namespace PharIo\Phive;

use PharIo\Version\Version;

interface Release {
    public function isSupported(): bool;

    public function getVersion(): Version;

    public function getName(): string;
}
