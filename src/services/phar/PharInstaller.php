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

use function basename;
use function chmod;
use function clearstatcache;
use function copy;
use function set_error_handler;
use function sprintf;
use PharIo\FileSystem\Directory;
use PharIo\FileSystem\DirectoryException;
use PharIo\FileSystem\File;
use PharIo\FileSystem\Filename;

abstract class PharInstaller {
    /** @var Cli\Output */
    private $output;

    /** @var mixed */
    private $originalHandler;

    public function __construct(Cli\Output $output) {
        $this->output = $output;
    }

    public function install(File $phar, Filename $destination, bool $copy): void {
        try {
            $this->registerLocalErrorHandler();

            $this->cleanupExisting($destination);
            $this->prepareDestinationDirectory($destination->getDirectory());

            if ($copy) {
                $this->copy($phar->getFilename(), $destination);
            } else {
                $this->link($phar->getFilename(), $destination);
            }

            $this->restoreErrorHandler();
        } catch (PharInstallerException $e) {
            throw new InstallationFailedException(
                sprintf('Installation failed: %s', $e->getMessage())
            );
        }
    }

    protected function getOutput(): Cli\Output {
        return $this->output;
    }

    protected function copy(Filename $phar, Filename $destination): void {
        $this->getOutput()->writeInfo(
            sprintf('Copying %s to %s', basename($phar->asString()), $destination->asString())
        );
        copy($phar->asString(), $destination->asString());
        chmod($destination->asString(), 0755);
    }

    /**
     * @throws LinkCreationFailedException
     */
    abstract protected function link(Filename $phar, Filename $destination): void;

    private function prepareDestinationDirectory(Directory $dir): void {
        if ($dir->exists() && !$dir->isWritable()) {
            throw new PharInstallerException(
                sprintf('Directory %s is not writable.', $dir->asString())
            );
        }

        try {
            $dir->ensureExists();
        } catch (DirectoryException $e) {
            throw new PharInstallerException(
                sprintf('Directory %s could not be created: %s', $dir->asString(), $e->getMessage()),
                (int)$e->getCode(),
                $e
            );
        }
    }

    private function cleanupExisting(Filename $destination): void {
        try {
            $destination->delete();
        } catch (PharInstallerException $e) {
        }
        clearstatcache(true, $destination->asString());

        if ($destination->exists()) {
            throw new PharInstallerException(
                sprintf('Existing file %s could not be removed', $destination->asString())
            );
        }
    }

    private function registerLocalErrorHandler(): void {
        $this->originalHandler = set_error_handler(
            static function (int $errno, string $errstr): void {
                throw new PharInstallerException($errstr, $errno);
            }
        );
    }

    private function restoreErrorHandler(): void {
        set_error_handler($this->originalHandler);
    }
}
