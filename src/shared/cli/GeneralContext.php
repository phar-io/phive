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

use function array_key_exists;
use function array_search;
use function in_array;
use function sprintf;
use function strlen;

abstract class GeneralContext implements Context {
    /** @var Options */
    private $options;

    public function __construct() {
        $this->options = new Options();
    }

    public function addArgument(string $arg): void {
        $this->options->addArgument($arg);
    }

    /**
     * @throws ContextException
     */
    public function setOption(string $option, $value): void {
        $this->ensureNotConflicting($option);
        $this->options->setOption($option, $value);
    }

    public function getOptions(): Options {
        return $this->options;
    }

    public function canContinue(): bool {
        return true;
    }

    public function knowsOption(string $option): bool {
        return array_key_exists($option, $this->getKnownOptions());
    }

    public function requiresValue(string $option): bool {
        return false;
    }

    /**
     * @throws ContextException
     */
    public function hasOptionForChar(string $char): bool {
        if (strlen($char) !== 1) {
            throw new ContextException('short option must be a string of length 1');
        }

        return in_array($char, $this->getKnownOptions(), true);
    }

    /**
     * @throws ContextException
     */
    public function getOptionForChar(string $char): string {
        if (!$this->hasOptionForChar($char)) {
            throw new ContextException('No short option with this char');
        }

        return (string) array_search($char, $this->getKnownOptions(), true);
    }

    public function acceptsArguments(): bool {
        return true;
    }

    /**
     * Return Options array.
     *
     * Format: (key == name, value = short-char or false, e.g. ['long' => 'l', 'other' => false])
     * Return empty array if no options are supported
     */
    protected function getKnownOptions(): array {
        return [];
    }

    /**
     * Return conflicting pairs of options.
     *
     * Format: Array of pairs (e.g. [ ['a' => 'b'], ['a' => 'c'] ])
     * Lookup is performed both ways
     * Return empty array if no options are conflicting
     */
    protected function getConflictingOptions(): array {
        return [];
    }

    private function ensureNotConflicting(string $option): void {
        $list = $this->getConflictingOptions();

        foreach ($list as $pair) {
            foreach ($pair as $opt1 => $opt2) {
                if ($option !== $opt1 && $option !== $opt2) {
                    continue;
                }

                if (($option === $opt1 && $this->options->hasOption($opt2)) ||
                    ($option === $opt2 && $this->options->hasOption($opt1))
                ) {
                    throw new ContextException(
                        sprintf("Options '%s' and '%s' cannot be combined", $opt1, $opt2),
                        ContextException::ConflictingOptions
                    );
                }
            }
        }
    }
}
