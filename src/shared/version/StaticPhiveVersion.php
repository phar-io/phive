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

class StaticPhiveVersion extends PhiveVersion {
    /** @var string */
    private $version;

    public function __construct(string $version) {
        $this->version = $version;
    }

    public function getVersion(): string {
        return $this->version;
    }
}
