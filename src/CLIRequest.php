<?php
namespace TheSeer\Phive {

    class CLIRequest {

        /**
         * @var string[]
         */
        private $argv;

        /**
         * @var string
         */
        private $command;

        /**
         * @var CLICommandOptions
         */
        private $options;

        /**
         * @param array $argv
         */
        public function __construct(array $argv) {
            $this->argv = $argv;
        }

        public function getCommand() {
            $this->parse();
            return $this->command;
        }

        /**
         * @return CLICommandOptions
         */
        public function getCommandOptions() {
            $this->parse();
            return $this->options;
        }

        private function parse() {
            if ($this->command !== NULL) {
                return;
            }

            if (count($this->argv) == 1) {
                $this->command = 'help';
                $this->options = new CLICommandOptions([]);
                return;
            }

            if (count($this->argv) >= 2) {
                $this->command = $this->argv[1];
                $this->options = new CLICommandOptions(array_slice($this->argv, 2));
                return;
            }

            $this->options = new CLICommandOptions([]);
        }

    }

}
