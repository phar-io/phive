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

interface Context {
    public function canContinue(): bool;

    public function knowsOption(string $option): bool;

    public function requiresValue(string $option): bool;

    public function getOptionForChar(string $char): string;

    public function hasOptionForChar(string $char): bool;

    public function acceptsArguments(): bool;

    public function addArgument(string $arg): void;

    /**
     * @param mixed $value
     */
    public function setOption(string $option, $value): void;

    public function getOptions(): Options;
}
