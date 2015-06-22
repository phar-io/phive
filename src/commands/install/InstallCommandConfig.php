<?php
namespace TheSeer\Phive {

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
         * @return mixed
         * @throws CLICommandOptionsException
         */
        public function getPharName() {
            $filename = pathinfo($this->cliOptions->getArgument(0), PATHINFO_FILENAME);
            preg_match('/(.*)-[0-9].[0-9].[0-9].*/',$filename, $matches);
            return $matches[1];
        }

        /**
         * @return Version
         * @throws CLICommandOptionsException
         */
        public function getPharVersion() {
            $filename = pathinfo($this->cliOptions->getArgument(0), PATHINFO_FILENAME);
            preg_match('/-([0-9].[0-9].[0-9].*)/',$filename, $matches);
            return new Version($matches[1]);
        }


        /**
         * @return Url
         * @throws CLICommandOptionsException
         */
        public function getSignatureUrl() {
            return new Url($this->cliOptions->getArgument(0) . '.asc');
        }

        /**
         * @return bool
         */
        public function makeCopy() {
            return $this->cliOptions->isSwitch('copy');
        }
    }

}
