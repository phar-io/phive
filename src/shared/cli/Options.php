<?php declare(strict_types = 1);
namespace PharIo\Phive\Cli;

class Options {

    /** @var array<string, mixed> */
    private $options = [];

    /** @var string[] */
    private $arguments = [];

    public function setOption(string $option, $value): void {
        $this->options[$option] = $value;
    }

    public function hasOption(string $name): bool {
        return isset($this->options[$name]);
    }

    /**
     * @throws CommandOptionsException
     */
    public function getOption(string $name) {
        if (!$this->hasOption($name)) {
            throw new CommandOptionsException(
                \sprintf('No option with name %s', $name),
                CommandOptionsException::NoSuchOption
            );
        }

        return $this->options[$name];
    }

    public function addArgument(string $argument): void {
        $this->arguments[] = $argument;
    }

    public function getArgumentCount(): int {
        return \count($this->arguments);
    }

    public function getArgument(int $index): string {
        if (!$this->hasArgument($index)) {
            throw new CommandOptionsException(
                \sprintf('No argument at index %s', $index),
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
        $result->options   = \array_merge($this->options, $options->options);

        return $result;
    }
}
