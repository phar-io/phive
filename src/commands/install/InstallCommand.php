<?php
namespace PharIo\Phive {

    use TheSeer\CLI\Command;

    class InstallCommand implements Command {

        /**
         * @var InstallCommandConfig
         */
        private $config;

        /**
         * @var PharService
         */
        private $pharService;

        /**
         * @param InstallCommandConfig $config
         * @param PharService          $pharService
         */
        public function __construct(InstallCommandConfig $config, PharService $pharService) {
            $this->config = $config;
            $this->pharService = $pharService;
        }

        /**
         *
         */
        public function execute() {
            foreach ($this->config->getRequestedPhars() as $requestedPhar) {
                $this->pharService->install($requestedPhar, $this->config->getWorkingDirectory());
            }
        }

    }

}
