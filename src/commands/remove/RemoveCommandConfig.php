<?php
namespace PharIo\Phive {

    use TheSeer\CLI;

    class RemoveCommandConfig {

        /**
         * @var CLI\CommandOptions
         */
        private $cliOptions;

        /**
         * @var Config
         */
        private $config;

        /**
         * InstallCommandConfig constructor.
         *
         * @param CLI\CommandOptions $options
         * @param Config            $config
         */
        public function __construct(CLI\CommandOptions $options, Config $config) {
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
         * @throws CLI\CommandOptionsException
         */
        public function getPharName() {
            return $this->cliOptions->getArgument(0);
        }

    }

}
