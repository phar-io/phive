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
use PharIo\FileSystem\Filename;

class ComposerCommandConfig extends InstallCommandConfig {
    /** @var Directory */
    private $workingDirectory;

    public function __construct(
        Cli\Options $options,
        PhiveXmlConfig $phiveXmlConfig,
        Environment $environment,
        TargetDirectoryLocator $targetDirectoryLocator,
        Directory $workingDirectory
    ) {
        parent::__construct($options, $phiveXmlConfig, $environment, $targetDirectoryLocator);
        $this->workingDirectory = $workingDirectory;
    }

    public function getComposerFilename(): Filename {
        return $this->workingDirectory->file('composer.json');
    }
}
