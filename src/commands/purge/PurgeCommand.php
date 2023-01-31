<?php declare(strict_types=1);
/*
 * This file is part of Phive.
 *
 * Copyright (c) Arne Blankerts <arne@blankerts.de>, Sebastian Heuer <sebastian@phpeople.de> and contributors
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */
namespace PharIo\Phive;

use function sprintf;

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
                sprintf(
                    'Phar %s %s has been deleted.',
                    $unusedPhar->getName(),
                    $unusedPhar->getVersion()->getVersionString()
                )
            );
        }
    }
}
