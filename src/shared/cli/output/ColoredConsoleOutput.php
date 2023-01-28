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

use function preg_replace_callback;
use function sprintf;

class ColoredConsoleOutput extends ConsoleOutput {
    public function writeError(string $errorMessage): void {
        $errorMessage = sprintf("\033[0;31m %s \033[0m", $errorMessage);
        parent::writeError($errorMessage);
    }

    public function writeWarning(string $warningMessage): void {
        $warningMessage = sprintf("\033[1;33m %s \033[0m", $warningMessage);
        parent::writeWarning($warningMessage);
    }

    public function writeMarkdown(string $markdown): void {
        // bold => yellow
        $markdown = preg_replace_callback('/(\*\*|__)(.*?)\1/', static function (array $matches): string {
            return "\033[33m" . $matches[2] . "\033[0m"; // 0m
        }, $markdown);

        // italic => green
        $markdown = preg_replace_callback('/(\*|_)(.*?)\1/', static function (array $matches): string {
            return "\033[32m" . $matches[2] . "\033[0m";
        }, $markdown);

        $this->writeText($markdown);
    }
}
