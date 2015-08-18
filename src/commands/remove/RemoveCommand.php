<?php
namespace PharIo\Phive {

    use TheSeer\CLI;

    class RemoveCommand implements CLI\Command {

        /**
         * @var RemoveCommandConfig
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
         * @param RemoveCommandConfig  $config
         * @param PharRepository       $repository
         * @param PharService          $pharService
         * @param Logger               $logger
         */
        public function __construct(
            RemoveCommandConfig $config, PharRepository $repository, PharService $pharService, Logger $logger
        ) {
            $this->config = $config;
            $this->repository = $repository;
            $this->pharService = $pharService;
            $this->logger = $logger;
        }

        public function execute() {
            $destination = $this->config->getWorkingDirectory() . '/' . $this->config->getPharName();
            $phar = $this->repository->getByUsage($destination);
            $this->logger->logInfo(
                sprintf('Removing Phar %s %s', $phar->getName(), $phar->getVersion()->getVersionString())
            );
            $this->repository->removeUsage($phar, $destination);
            unlink($destination);

            if (!$this->repository->hasUsages($phar)) {
                $this->logger->logInfo(
                    sprintf(
                        'Phar %s %s has no more known usages. You can run \'phive purge\' to remove unused Phars.', $phar->getName(), $phar->getVersion()->getVersionString()
                    )
                );
            }
        }

    }

}
