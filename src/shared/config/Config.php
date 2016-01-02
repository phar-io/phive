<?php
namespace PharIo\Phive;

class Config {

    /**
     * @var Environment
     */
    private $environment;

    /**
     * @param Environment $environment
     */
    public function __construct(Environment $environment) {
        $this->environment = $environment;
    }

    /**
     * @return Directory
     */
    public function getHomeDirectory() {
        return $this->environment->getHomeDirectory()->child('.phive');
    }

    /**
     * @return Directory
     */
    public function getWorkingDirectory() {
        return $this->environment->getWorkingDirectory();
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
    public function getRepositoryListUrl() {
        return new Url('https://phar.io/data/repositories.xml');
    }

}



