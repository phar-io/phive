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

class OutputFactory {
    public function getConsoleOutput(bool $outputProgress): Output {
        return new ConsoleOutput(ConsoleOutput::VERBOSE_INFO, $outputProgress);
    }

    public function getColoredConsoleOutput(bool $outputProgress): Output {
        return new ColoredConsoleOutput(ConsoleOutput::VERBOSE_INFO, $outputProgress);
    }
}
