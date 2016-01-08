<?php
namespace PharIo\Phive;

use PharIo\Phive\Cli;

class SkelCommandConfig {

    /**
     * @var CLI\Options
     */
    private $cliOptions;

    /**
     * @var string
     */
    private $workingDirectory = '';

    /**
     * @param CLI\Options        $cliOptions
     * @param                    $workingDirectory
     */
    public function __construct(CLI\Options $cliOptions, $workingDirectory) {
        $this->cliOptions = $cliOptions;
        $this->workingDirectory = rtrim($workingDirectory, '/');

    }

    /**
     * @return bool
     */
    public function allowOverwrite() {
        return $this->cliOptions->isSwitch('force');
    }

    /**
     * @return string
     */
    public function getDestination() {
        return $this->workingDirectory . '/phive.xml';
    }

    /**
     * @return string
     */
    public function getTemplateFilename() {
        return __DIR__ . '/../../../conf/phive.skeleton.xml';
    }
}
