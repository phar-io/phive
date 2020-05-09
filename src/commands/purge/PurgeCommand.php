<?php declare(strict_types = 1);
namespace PharIo\Phive;

class PurgeCommand implements Cli\Command {

    /** @var PharRegistry */
    private $repository;

    /** @var Cli\Output */
    private $output;

    public function __construct(
        PharRegistry $repository,
        Cli\Output $output
    ) {
        $this->repository = $repository;
        $this->output     = $output;
    }

    public function execute(): void {
        foreach ($this->repository->getUnusedPhars() as $unusedPhar) {
            $this->repository->removePhar($unusedPhar);
            $this->output->writeInfo(
                \sprintf(
                    'Phar %s %s has been deleted.',
                    $unusedPhar->getName(),
                    $unusedPhar->getVersion()->getVersionString()
                )
            );
        }
    }
}
