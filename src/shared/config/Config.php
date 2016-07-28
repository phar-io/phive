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
        return new Directory('tools');
    }

    /**
     * @return Filename
     * @throws NoGPGBinaryFoundException
     */
    public function getGPGBinaryPath() {
        try {
            return $this->environment->getPathToCommand('gpg');
        } catch (EnvironmentException $e) {
            $message = "No executable gpg binary found. \n Either install gpg or enable the gnupg extension in PHP.";
            throw new NoGPGBinaryFoundException($message);
        }
    }

    /**
     * @return Url
     */
    public function getSourcesListUrl() {
        return new Url('https://phar.io/data/repositories.xml');
    }

    public function getTrustedKeyIds() {
        $idList = new KeyIdCollection();
        if ($this->cliOptions->hasOption('trust-gpg-keys')) {
            foreach(explode(',', $this->cliOptions->getOption('trust-gpg-keys')) as $id) {
                $idList->addKeyId($id);
            }
        }
        return $idList;
    }

}
