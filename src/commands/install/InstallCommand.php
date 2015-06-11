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
         * @param InstallService       $service
         * @param InstallCommandConfig $config
         */
        public function __construct(InstallService $service, InstallCommandConfig $config) {
            $this->service = $service;
            $this->config = $config;
        }

        public function execute() {
            $phar = $this->service->downloadPhar($this->config->getPharUrl());
            $signature = $this->service->downloadSignature($this->config->getSignatureUrl());
            if (!$this->service->verifySignature($phar, $signature)) {
                throw new VerificationFailedException();
            }
            $this->service->installPhar($phar, $this->config->makeCopy());
        }

    }

}
