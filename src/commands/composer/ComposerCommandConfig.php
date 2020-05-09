<?php declare(strict_types = 1);
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
