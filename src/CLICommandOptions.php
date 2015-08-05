<?php
namespace PharIo\Phive {

    class CLICommandOptions {

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
         * @throws CLICommandOptionsException
         */
        public function getOption($name) {
            if (!$this->hasOption($name)) {
                throw new CLICommandOptionsException(
                    sprintf('No option with name %s', $name),
                    CLICommandOptionsException::NoSuchOption
                );
            }
            return $this->options[$name];
        }

        public function isSwitch($switch) {
            return isset($this->switches[$switch]);
        }

        public function getArgumentCount() {
            return count($this->arguments);
        }

        public function hasArgument($index) {
            return isset($this->arguments[$index]);
        }

        public function getArgument($index) {
            if (!$this->hasArgument($index)) {
                throw new CLICommandOptionsException(
                    sprintf('No argument at index %s', $index),
                    CLICommandOptionsException::InvalidArgumentIndex
                );
            }
            return $this->arguments[$index];
        }

        private function parseOptions(array $options) {
            $skipNext = false;
            foreach($options as $idx => $option) {
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
                    $this->options[ltrim($option,'-')] = $options[$idx + 1];
                    $skipNext = true;
                    continue;
                }
                if (strpos($option, '-') === 0) {
                    $this->switches[ltrim($option,'-')] = true;
                    continue;
                }
                $this->arguments[] = $option;
            }
        }
    }

}
