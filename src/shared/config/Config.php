<?php
namespace PharIo\Phive;

use PharIo\Phive\Cli\Options;

class Config {

    /**
     * @var Environment
     */
    private $environment;

    /**
     * @var Options
     */
    private $cliOptions;

    /**
     * @param Environment $environment
     * @param Options     $cliOptions
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
     * @return Filename
     * @throws NoGPGBinaryFoundException
     */
    public function getGPGBinaryPath() {
        $possiblePaths = [
            '/usr/bin/gpg',         // Linux default
            '/usr/local/bin/gpg'    // OSX default
        ];
        foreach ($possiblePaths as $possiblePath) {
            $file = new Filename($possiblePath);
            if (!$file->exists() || !$file->isExecutable()) {
                continue;
            }
            return $file;
        }
        $message = sprintf(
            "No executable gpg binary found in any of the following locations: \n"
            . "%s \n"
            . "Either install gpg or enable the gnupg extension in PHP.",
            implode("\n", $possiblePaths)
        );
        throw new NoGPGBinaryFoundException($message);
    }

    /**
     * @return Url
     */
    public function getSourcesListUrl() {
        return new Url('https://phar.io/data/repositories.xml');
    }

}
