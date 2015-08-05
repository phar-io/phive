<?php
namespace PharIo\Phive {

    class InstallCommandConfig {

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
         * @return Url
         * @throws CLICommandOptionsException
         */
        public function getPharUrl() {
            return new Url($this->cliOptions->getArgument(0));
        }

        /**
         * @return bool
         */
        public function makeCopy() {
            return $this->cliOptions->isSwitch('copy');
        }
    }

}
