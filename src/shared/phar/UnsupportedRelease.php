<?php declare(strict_types=1);
/*
 * This file is part of Phive.
 *
 * Copyright (c) Arne Blankerts <arne@blankerts.de>, Sebastian Heuer <sebastian@phpeople.de> and contributors
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */
namespace PharIo\Phive;

use PharIo\Version\Version;

class UnsupportedRelease implements Release {
    /** @var string */
    private $name;

    /** @var Version */
    private $version;

    /** @var string */
    private $reason;

    public function __construct(string $name, Version $version, string $reason) {
        $this->name    = $name;
        $this->version = $version;
        $this->reason  = $reason;
    }

    public function isSupported(): bool {
        return false;
    }

    public function getVersion(): Version {
        return $this->version;
    }

    public function getReason(): string {
        return $this->reason;
    }

    public function getName(): string {
        return $this->name;
    }
}
