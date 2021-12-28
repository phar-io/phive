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
namespace PharIo\Phive\Cli;

use const STDERR;
use const STDOUT;
use function fwrite;
use function in_array;
use function preg_replace_callback;
use InvalidArgumentException;

class ConsoleOutput implements Output {
    public const VERBOSE_ERROR   = 1;
    public const VERBOSE_WARNING = 2;
    public const VERBOSE_INFO    = 3;

    /** @var int */
    private $verbosity = self::VERBOSE_INFO;

    /** @var bool */
    private $printProgressUpdates;

    public function __construct(int $verbosity, bool $printProgressUpdates) {
        $this->setVerbosity($verbosity);
        $this->printProgressUpdates = $printProgressUpdates;
    }

    public function writeInfo(string $infoMessage): void {
        if ($this->verbosity >= self::VERBOSE_INFO) {
            $this->writeText($infoMessage . "\n");
        }
    }

    public function writeProgress(string $progressMessage): void {
        if ($this->verbosity >= self::VERBOSE_INFO && $this->printProgressUpdates) {
            $this->writeText("\x0D\x1B[2K" . $progressMessage);
        }
    }

    public function writeText(string $textMessage): void {
        fwrite(STDOUT, $textMessage);
    }

    public function writeWarning(string $warningMessage): void {
        if ($this->verbosity >= self::VERBOSE_WARNING) {
            $this->writeText('[WARNING] ' . $warningMessage . "\n");
        }
    }

    public function writeError(string $errorMessage): void {
        if ($this->verbosity >= self::VERBOSE_ERROR) {
            fwrite(STDERR, '[ERROR]   ' . $errorMessage . "\n");
        }
    }

    public function writeMarkdown(string $markdown): void {
        // bold => yellow
        $markdown = preg_replace_callback('/(\*\*|__)(.*?)\1/', static function (array $matches): string {
            return $matches[2];
        }, $markdown);

        // italic => green
        $markdown = preg_replace_callback('/(\*|_)(.*?)\1/', static function (array $matches): string {
            return $matches[2];
        }, $markdown);

        $this->writeText($markdown);
    }

    /**
     * @param int $verbosity
     */
    private function setVerbosity($verbosity): void {
        if (!in_array($verbosity, [self::VERBOSE_ERROR, self::VERBOSE_INFO, self::VERBOSE_WARNING], true)) {
            throw new InvalidArgumentException('Invalid value for verbosity');
        }
        $this->verbosity = $verbosity;
    }
}
