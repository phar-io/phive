<?php
namespace PharIo\Phive;

use PharIo\Phive\Cli\Options;

class Config {

    /**
     * @var Environment
     */
    private $environment;

    /**
     * @var Config
     */
    private $cliOptions;

    /**
     * @param Environment $environment
     * @param Options $cliOptions
     */
    public function __construct(Environment $environment, Options $cliOptions) {
        $this->environment = $environment;
        $this->cliOptions = $cliOptions;
    }

    /**
     * @return Directory
     */
    public function getHomeDirectory() {
        if ($this->cliOptions->hasOption('home')) {
            return new Directory($this->cliOptions->getOption('home'));
        }
        return $this->environment->getHomeDirectory()->child('.phive');
    }

    /**
     * @return Directory
     */
    public function getWorkingDirectory() {
        return $this->environment->getWorkingDirectory();
    }

    /**
     * @return Directory
     */
    public function getToolsDirectory() {
        return $this->getWorkingDirectory()->child('tools');
    }
    
    /**
     * @return string
     */
    public function getGPGBinaryPath() {
        return '/usr/bin/gpg';
    }

    /**
     * @return Url
     */
    public function getSourcesListUrl() {
        return new Url('https://phar.io/data/repositories.xml');
    }

}
