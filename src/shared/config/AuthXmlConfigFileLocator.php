<?php declare(strict_types = 1);
namespace PharIo\Phive;

class AuthXmlConfigFileLocator {
    private const FILENAME = 'phive-auth.xml';

    /** @var Config */
    private $config;

    /** @var Environment */
    private $environment;

    public function __construct(Environment $environment, Config $config) {
        $this->environment = $environment;
        $this->config      = $config;
    }

    public function getFile(bool $global): \PharIo\FileSystem\Filename {
        if ($global) {
            return $this->config->getHomeDirectory()->file(self::FILENAME);
        }

        return $this->environment->getWorkingDirectory()->file(self::FILENAME);
    }
}
