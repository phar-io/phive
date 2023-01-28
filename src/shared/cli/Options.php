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

use function array_merge;
use function count;
use function sprintf;

class Options {
    /** @var array<string, mixed> */
    private $options = [];

    /** @var string[] */
    private $arguments = [];

    /**
     * @param mixed $value
     */
    public function setOption(string $option, $value): void {
        $this->options[$option] = $value;
    }

    public function hasOption(string $name): bool {
        return isset($this->options[$name]);
    }

    /**
     * @throws CommandOptionsException
     *
     * @return mixed
     */
    public function getOption(string $name) {
        if (!$this->hasOption($name)) {
            throw new CommandOptionsException(
                sprintf('No option with name %s', $name),
                CommandOptionsException::NoSuchOption
            );
        }

        return $this->options[$name];
    }

    public function addArgument(string $argument): void {
        $this->arguments[] = $argument;
    }

    public function getArgumentCount(): int {
        return count($this->arguments);
    }

    public function getArgument(int $index): string {
        if (!$this->hasArgument($index)) {
            throw new CommandOptionsException(
                sprintf('No argument at index %s', $index),
                CommandOptionsException::InvalidArgumentIndex
            );
        }

        return $this->arguments[$index];
    }

    public function hasArgument(int $index): bool {
        return isset($this->arguments[$index]);
    }

    public function getArguments(): array {
        return $this->arguments;
    }

    public function mergeOptions(self $options): self {
        $result            = new self();
        $result->arguments = $this->arguments;
        $result->options   = array_merge($this->options, $options->options);

        return $result;
    }
}
