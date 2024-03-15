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

use function file_exists;
use PharIo\FileSystem\File;
use PharIo\Manifest\Manifest;
use PharIo\Manifest\ManifestLoader;
use PharIo\Manifest\ManifestLoaderException;
use PharIo\Version\Version;

class Phar {
    /** @var string */
    private $name;

    /** @var Version */
    private $version;

    /** @var File */
    private $file;

    /** @var null|string */
    private $signatureFingerprint;

    public function __construct(string $name, Version $version, File $file, ?string $signatureFingerprint = null) {
        $this->name                 = $name;
        $this->file                 = $file;
        $this->version              = $version;
        $this->signatureFingerprint = $signatureFingerprint;
    }

    public function getName(): string {
        return $this->name;
    }

    public function getVersion(): Version {
        return $this->version;
    }

    public function getFile(): File {
        return $this->file;
    }

    /** @psalm-assert-if-true string $this->signatureFingerprint */
    public function hasSignatureFingerprint(): bool {
        return $this->signatureFingerprint !== null;
    }

    /**
     * @throws PharException
     */
    public function getSignatureFingerprint(): string {
        if (!$this->hasSignatureFingerprint()) {
            throw new PharException('No signature fingerprint set');
        }

        return $this->signatureFingerprint;
    }

    public function hasManifest(): bool {
        return file_exists(
            'phar://' .
            $this->file->getFilename()->asString() .
            '/manifest.xml'
        );
    }

    /**
     * @throws PharException
     */
    public function getManifest(): Manifest {
        try {
            return ManifestLoader::fromPhar($this->file->getFilename()->asString());
        } catch (ManifestLoaderException $e) {
            throw new PharException('Loading manifest failed.', 0, $e);
        }
    }
}
