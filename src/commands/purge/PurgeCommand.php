<?php
namespace PharIo\Phive;

use PharIo\Phive\Cli;

class PurgeCommand implements Cli\Command {

    /**
     * @var PurgeCommandConfig
     */
    private $config;

    /**
     * @var PhiveInstallDB
     */
    private $repository;

    /**
     * @var Cli\Output
     */
    private $output;

    /**
     * @param PurgeCommandConfig $config
     * @param PhiveInstallDB     $repository
     * @param Cli\Output         $output
     */
    public function __construct(
        PurgeCommandConfig $config, PhiveInstallDB $repository, Cli\Output $output
    ) {
        $this->config = $config;
        $this->repository = $repository;
        $this->output = $output;
    }

    public function execute() {

        foreach ($this->repository->getUnusedPhars() as $unusedPhar) {
            $this->repository->removePhar($unusedPhar);
            $this->output->writeInfo(
                sprintf(
                    'Phar %s %s has been deleted.',
                    $unusedPhar->getName(),
                    $unusedPhar->getVersion()->getVersionString()
                )
            );
        }

    }

}
