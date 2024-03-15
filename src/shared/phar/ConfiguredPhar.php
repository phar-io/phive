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

use PharIo\FileSystem\Filename;
use PharIo\Version\Version;
use PharIo\Version\VersionConstraint;

class ConfiguredPhar {
    /** @var string */
    private $name;

    /** @var VersionConstraint */
    private $versionConstraint;

    /** @var null|Version */
    private $installedVersion;

    /** @var null|Filename */
    private $location;

    /** @var null|PharUrl */
    private $url;

    /** @var bool */
    private $copy;

    public function __construct(
        string $name,
        VersionConstraint $versionConstraint,
        ?Version $installedVersion = null,
        ?Filename $location = null,
        ?PharUrl $url = null,
        bool $copy = false
    ) {
        $this->name              = $name;
        $this->versionConstraint = $versionConstraint;
        $this->installedVersion  = $installedVersion;
        $this->location          = $location;
        $this->url               = $url;
        $this->copy              = $copy;
    }

    public function getName(): string {
        return $this->name;
    }

    public function getVersionConstraint(): VersionConstraint {
        return $this->versionConstraint;
    }

    /**
     * @throws ConfiguredPharException
     */
    public function getInstalledVersion(): Version {
        if (!$this->isInstalled()) {
            throw new ConfiguredPharException('Phar is not installed');
        }

        return $this->installedVersion;
    }

    /** @psalm-assert-if-true Version $this->installedVersion */
    public function isInstalled(): bool {
        return $this->installedVersion !== null;
    }

    /** @psalm-assert !null $this->location */
    public function hasLocation(): bool {
        return $this->location !== null;
    }

    /**
     * @throws ConfiguredPharException
     */
    public function getLocation(): Filename {
        if (!$this->hasLocation()) {
            throw new ConfiguredPharException(
                'No location set',
                ConfiguredPharException::NoLocation
            );
        }

        return $this->location;
    }

    /** @psalm-assert !null $this->url */
    public function hasUrl(): bool {
        return $this->url !== null;
    }

    /**
     * @throws ConfiguredPharException
     */
    public function getUrl(): PharUrl {
        if (!$this->hasUrl()) {
            throw new ConfiguredPharException('No URL set', ConfiguredPharException::NoUrl);
        }

        return $this->url;
    }

    public function isCopy(): bool {
        return $this->copy;
    }
}
