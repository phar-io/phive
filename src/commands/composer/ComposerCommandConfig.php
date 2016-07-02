<?php
namespace PharIo\Phive;

use PharIo\Phive\Cli;

class ComposerCommandConfig extends InstallCommandConfig {

    /**
     * @var Directory
     */
    private $workingDirectory;

    /**
     * @param Cli\Options            $options
     * @param PhiveXmlConfig         $phiveXmlConfig
     * @param TargetDirectoryLocator $targetDirectoryLocator
     * @param Directory              $workingDirectory
     */
    public function __construct(
        Cli\Options $options,
        PhiveXmlConfig $phiveXmlConfig,
        TargetDirectoryLocator $targetDirectoryLocator,
        Directory $workingDirectory
    ) {
        parent::__construct($options, $phiveXmlConfig, $targetDirectoryLocator);
        $this->workingDirectory = $workingDirectory;
    }

    /**
     * @return Filename
     */
    public function getComposerFilename() {
        return $this->workingDirectory->file('composer.json');
    }

}
