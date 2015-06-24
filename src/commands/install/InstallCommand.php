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
            $destination = $this->getDestination($phar->getFile());
            $this->pharService->install($phar->getFile(), $destination, $this->config->makeCopy());
            $this->repository->addUsage($phar, $destination);

        }

        /**
         * @param File $pharFile
         *
         * @return string
         */
        private function getDestination(File $pharFile) {
            $filename = pathinfo($pharFile->getFilename(), PATHINFO_FILENAME);
            return $this->config->getWorkingDirectory() . DIRECTORY_SEPARATOR . $filename;
        }


    }

}
