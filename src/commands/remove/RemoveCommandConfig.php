<?php
namespace TheSeer\Phive {

    class RemoveCommandConfig {

        /**
         * @var CLICommandOptions
         */
        private $cliOptions;

        /**
         * @var Config
         */
        private $config;

        /**
         * InstallCommandConfig constructor.
         *
         * @param CLICommandOptions $options
         * @param Config            $config
         */
        public function __construct(CLICommandOptions $options, Config $config) {
            $this->cliOptions = $options;
            $this->config = $config;
        }

        /**
         * @return Directory
         */
        public function getWorkingDirectory() {
            return $this->config->getWorkingDirectory();
        }

        /**
         * @return string
         * @throws CLICommandOptionsException
         */
        public function getPharName() {
            return $this->cliOptions->getArgument(0);
        }

    }

}
