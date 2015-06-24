<?php
namespace TheSeer\Phive {

    class InstallCommand implements Command {

        /**
         * @var InstallCommandConfig
         */
        private $config;

        /**
         * @var PharRepository
         */
        private $repository;

        /**
         * @var PharService
         */
        private $pharService;

        /**
         * @var Logger
         */
        private $logger;

        /**
         * InstallCommand constructor.
         *
         * @param InstallCommandConfig $config
         * @param PharRepository       $repository
         * @param PharService          $pharService
         * @param Logger               $logger
         */
        public function __construct(
            InstallCommandConfig $config, PharRepository $repository, PharService $pharService, Logger $logger
        ) {
            $this->config = $config;
            $this->repository = $repository;
            $this->pharService = $pharService;
            $this->logger = $logger;
        }

        public function execute() {
            $phar = $this->repository->getByUrl($this->config->getPharUrl());
            $destination = $this->getDestination($phar);
            $this->pharService->install($phar->getFile(), $destination, $this->config->makeCopy());
            $this->repository->addUsage($phar, $destination);

        }

        /**
         * @param Phar $phar
         *
         * @return string
         */
        private function getDestination(Phar $phar) {
            return $this->config->getWorkingDirectory() . DIRECTORY_SEPARATOR . $phar->getName();
        }


    }

}
