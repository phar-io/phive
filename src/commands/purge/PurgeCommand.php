<?php
namespace PharIo\Phive {

    use TheSeer\CLI;

    class PurgeCommand implements CLI\Command {

        /**
         * @var PurgeCommandConfig
         */
        private $config;

        /**
         * @var PharRepository
         */
        private $repository;

        /**
         * @var Output
         */
        private $output;

        /**
         * @param PurgeCommandConfig $config
         * @param PharRepository     $repository
         * @param Output             $output
         */
        public function __construct(
            PurgeCommandConfig $config, PharRepository $repository, Output $output
        ) {
            $this->config = $config;
            $this->repository = $repository;
            $this->output = $output;
        }

        public function execute() {

            foreach ($this->repository->getUnusedPhars() as $unusedPhar) {
                $this->repository->removePhar($unusedPhar);
                $this->output->writeInfo(
                    sprintf('Phar %s %s has been deleted.', $unusedPhar->getName(), $unusedPhar->getVersion()->getVersionString())
                );
            }

        }

    }

}
