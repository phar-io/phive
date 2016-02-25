<?php
namespace PharIo\Phive\Cli;

class Options {

    /**
     * @var string[]
     */
    private $switches = [];

    /**
     * @var string[]
     */
    private $options = [];

    /**
     * @var string[]
     */
    private $arguments = [];

    /**
     * @param string[] $options
     */
    public function __construct(array $options) {
        $this->parseOptions($options);
    }

    private function parseOptions(array $options) {
        $skipNext = false;
        foreach ($options as $idx => $option) {
            if ($skipNext) {
                $skipNext = false;
                continue;
            }
            if (strpos($option, '--') === 0) {
                if (strpos($option, '=') !== false) {
                    list($key, $value) = explode('=', ltrim($option, '-'));
                    $this->options[$key] = $value;
                    continue;
                }
                if (isset($options[$idx + 1])) {
                    $this->options[ltrim($option, '-')] = $options[$idx + 1];
                    $skipNext = true;
                } else {
                    $this->options[ltrim($option, '-')] = true;
                }
                continue;
            }
            if (strpos($option, '-') === 0) {
                $this->switches[ltrim($option, '-')] = true;
                continue;
            }
            $this->arguments[] = $option;
        }
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
     * @param $name
     *
     * @return bool
     */
    public function hasOption($name) {
        return isset($this->options[$name]);
    }

    public function isSwitch($switch) {
        return isset($this->switches[$switch]);
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
}
