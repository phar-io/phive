<?php declare(strict_types = 1);
namespace PharIo\Phive;

class PhiveXmlConfigFileLocator {
    /** @var Config */
    private $config;

    /** @var Environment */
    private $environment;

    /** @var Cli\Output */
    private $output;

    public function __construct(Environment $environment, Config $config, Cli\Output $output) {
        $this->environment = $environment;
        $this->config      = $config;
        $this->output      = $output;
    }

    public function getFile(): \PharIo\FileSystem\Filename {
        $primary  = $this->config->getProjectInstallation();
        $fallback = $this->environment->getWorkingDirectory()->file('phive.xml');

        if ($primary->exists() && $fallback->exists()) {
            $this->output->writeWarning('Both .phive/phars.xml and phive.xml shouldn\'t be defined at the same time. Please prefer using .phive/phars.xml');
        }

        if (!$primary->exists() && $fallback->exists()) {
            return $fallback;
        }

        return $primary;
    }
}
