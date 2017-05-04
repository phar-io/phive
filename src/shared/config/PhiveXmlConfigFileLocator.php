<?php

namespace PharIo\Phive;

class PhiveXmlConfigFileLocator {

    const FILENAME = 'phive.xml';

    /**
     * @var Config
     */
    private $config;

    /**
     * @var Environment
     */
    private $environment;

    /**
     * @param Environment $environment
     * @param Config $config
     */
    public function __construct(Environment $environment, Config $config) {
        $this->environment = $environment;
        $this->config = $config;
    }

    /**
     * @param bool $global
     *
     * @return \PharIo\FileSystem\Filename
     */
    public function getFile($global) {
        if ($global) {
            return $this->config->getHomeDirectory()->file(self::FILENAME);
        }
        return $this->environment->getWorkingDirectory()->file(self::FILENAME);
    }

}
