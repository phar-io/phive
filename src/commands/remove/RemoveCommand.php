<?php
namespace PharIo\Phive;

use PharIo\Phive\Cli;

class RemoveCommand implements Cli\Command {

    /**
     * @var RemoveCommandConfig
     */
    private $config;

    /**
     * @var PhiveInstallDB
     */
    private $repository;

    /**
     * @var PharService
     */
    private $pharService;

    /**
     * @varCli\Output
     */
    private $output;

    /**
     * @param RemoveCommandConfig $config
     * @param PhiveInstallDB      $repository
     * @param PharService         $pharService
     * @param Cli\Output          $output
     */
    public function __construct(
        RemoveCommandConfig $config, PhiveInstallDB $repository, PharService $pharService, Cli\Output $output
    ) {
        $this->config = $config;
        $this->repository = $repository;
        $this->pharService = $pharService;
        $this->output = $output;
    }

    public function execute() {
        $destination = $this->config->getWorkingDirectory() . '/' . $this->config->getPharName();
        $phar = $this->repository->getByUsage($destination);
        $this->output->writeInfo(
            sprintf('Removing Phar %s %s', $phar->getName(), $phar->getVersion()->getVersionString())
        );
        $this->repository->removeUsage($phar, $destination);
        unlink($destination);

        if (!$this->repository->hasUsages($phar)) {
            $this->output->writeInfo(
                sprintf(
                    'Phar %s %s has no more known usages. You can run \'phive purge\' to remove unused Phars.',
                    $phar->getName(),
                    $phar->getVersion()->getVersionString()
                )
            );
        }
    }

}
