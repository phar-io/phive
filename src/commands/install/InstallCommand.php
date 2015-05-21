<?php
namespace TheSeer\Phive {

    class InstallCommand implements CommandInterface {

        /**
         * @var InstallService
         */
        private $service;

        /**
         * @var InstallCommandConfig
         */
        private $config;

        /**
         * InstallCommand constructor.
         *
         * @param InstallCommandConfig $config
         */
        public function __construct(InstallService $service, InstallCommandConfig $config) {
            $this->service = $service;
            $this->config = $config;
        }

        public function execute() {
            $this->config->
        }

    }

}
