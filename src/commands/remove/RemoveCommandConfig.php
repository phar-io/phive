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

use PharIo\FileSystem\Directory;

class RemoveCommandConfig {
    /** @var Cli\Options */
    private $cliOptions;

    /** @var TargetDirectoryLocator */
    private $targetDirectoryLocator;

    public function __construct(Cli\Options $options, TargetDirectoryLocator $targetDirectoryLocator) {
        $this->cliOptions             = $options;
        $this->targetDirectoryLocator = $targetDirectoryLocator;
    }

    public function getTargetDirectory(): Directory {
        return $this->targetDirectoryLocator->getTargetDirectory();
    }

    /**
     * @throws Cli\CommandOptionsException
     */
    public function getPharName(): string {
        return $this->cliOptions->getArgument(0);
    }
}
