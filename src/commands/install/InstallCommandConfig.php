<?php
namespace TheSeer\Phive {

    class InstallCommandConfig {

        /**
         * @var CLICommandOptions
         */
        private $cliOptions;

        /**
         * InstallCommandConfig constructor.
         *
         * @param CLICommandOptions $options
         */
        public function __construct(CLICommandOptions $options) {
            $this->cliOptions = $options;
        }

        /**
         * @return Url
         * @throws CLICommandOptionsException
         */
        public function getPharUrl() {
            return new Url($this->cliOptions->getArgument(0));
        }

        /**
         * @return Url
         * @throws CLICommandOptionsException
         */
        public function getSignatureUrl() {
            return new Url($this->cliOptions->getArgument(0) . '.asc');
        }
    }

}
