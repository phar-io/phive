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

interface Output {
    public function writeText(string $textMessage): void;
    public function writeInfo(string $infoMessage): void;
    public function writeWarning(string $warningMessage): void;
    public function writeError(string $errorMessage): void;
    public function writeProgress(string $progressMessage): void;
    public function writeMarkdown(string $markdown): void;
}
