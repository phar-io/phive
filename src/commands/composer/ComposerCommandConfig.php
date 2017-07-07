<?php
namespace PharIo\Phive;

use PharIo\FileSystem\Directory;
use PharIo\FileSystem\Filename;
use PharIo\Phive\Cli;

class ComposerCommandConfig extends InstallCommandConfig {

    /**
     * @var Directory
     */
    private $workingDirectory;

    /**
     * @param Cli\Options            $options
     * @param PhiveXmlConfig         $phiveXmlConfig
     * @param Environment            $environment
     * @param TargetDirectoryLocator $targetDirectoryLocator
     * @param Directory              $workingDirectory
     */
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

    /**
     * @return Filename
     */
    public function getComposerFilename() {
        return $this->workingDirectory->file('composer.json');
    }

}
