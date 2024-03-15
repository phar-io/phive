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

class SupportedRelease implements Release {
    /** @var Version */
    private $version;

    /** @var PharUrl */
    private $url;

    /** @var null|Hash */
    private $expectedHash;

    /** @var string */
    private $name;

    /** @var null|Url */
    private $signatureUrl;

    public function __construct(string $name, Version $version, PharUrl $url, ?Url $signatureUrl = null, ?Hash $expectedHash = null) {
        $this->name         = $name;
        $this->version      = $version;
        $this->url          = $url;
        $this->signatureUrl = $signatureUrl;
        $this->expectedHash = $expectedHash;
    }

    public function isSupported(): bool {
        return true;
    }

    public function getName(): string {
        return $this->name;
    }

    public function getVersion(): Version {
        return $this->version;
    }

    public function getUrl(): PharUrl {
        return $this->url;
    }

    /** @psalm-assert-if-true Url $this->signatureUrl */
    public function hasSignatureUrl(): bool {
        return $this->signatureUrl !== null;
    }

    public function getSignatureUrl(): ?Url {
        return $this->signatureUrl;
    }

    /** @psalm-assert-if-true Hash $this->expectedHash */
    public function hasExpectedHash(): bool {
        return $this->expectedHash !== null;
    }

    public function getExpectedHash(): ?Hash {
        return $this->expectedHash;
    }
}
