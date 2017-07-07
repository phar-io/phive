<?php
namespace PharIo\Phive\Cli;

class Options {

    /**
     * @var string[]
     */
    private $options = [];

    /**
     * @var string[]
     */
    private $arguments = [];

    /**
     * @param string $option
     * @param mixed  $value
     */
    public function setOption($option, $value) {
        $this->options[$option] = $value;
    }

    /**
     * @param $name
     *
     * @return bool
     */
    public function hasOption($name) {
        return isset($this->options[$name]);
    }

    /**
     * @param $name
     *
     * @return string
     * @throws CommandOptionsException
     */
    public function getOption($name) {
        if (!$this->hasOption($name)) {
            throw new CommandOptionsException(
                sprintf('No option with name %s', $name),
                CommandOptionsException::NoSuchOption
            );
        }

        return $this->options[$name];
    }

    /**
     * @param string $argument
     */
    public function addArgument($argument) {
        $this->arguments[] = $argument;
    }

    public function getArgumentCount() {
        return count($this->arguments);
    }

    public function getArgument($index) {
        if (!$this->hasArgument($index)) {
            throw new CommandOptionsException(
                sprintf('No argument at index %s', $index),
                CommandOptionsException::InvalidArgumentIndex
            );
        }

        return $this->arguments[$index];
    }

    public function hasArgument($index) {
        return isset($this->arguments[$index]);
    }

    public function getArguments() {
        return $this->arguments;
    }

    public function mergeOptions(Options $options) {
        $result = new Options();
        $result->arguments = $this->arguments;
        $result->options = array_merge($this->options, $options->options);

        return $result;
    }

}
