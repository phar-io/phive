<?php
namespace PharIo\Phive;

use TheSeer\CLI\Command;

class SkelCommand implements Command {

    /**
     * @var SkelCommandConfig
     */
    private $config;

    /**
     * @var PhiveVersion
     */
    private $version;

    /**
     * @var \DateTime
     */
    private $now;

    /**
     * @param SkelCommandConfig $config
     * @param PhiveVersion      $version
     * @param \DateTime         $now
     */
    public function __construct(SkelCommandConfig $config, PhiveVersion $version, \DateTime $now = null) {
        $this->config = $config;
        $this->version = $version;
        if (null === $now) {
            $now = new \DateTime();
        }
        $this->now = $now;
    }

    /**
     *
     */
    public function execute() {
        $skeleton = file_get_contents($this->config->getTemplateFilename());
        $skeleton = $this->replacePlaceholder($skeleton, '%%VERSION%%', $this->version->getVersion());
        $skeleton = $this->replacePlaceholder($skeleton, '%%DATE%%', $this->now->format('Y-m-d H:i:sO'));
        $this->writeSkeletonFile($skeleton);
    }

    /**
     * @param string $content
     * @param string $placeholder
     * @param string $replacement
     *
     * @return string
     */
    private function replacePlaceholder($content, $placeholder, $replacement) {
        return str_replace($placeholder, $replacement, $content);
    }

    /**
     * @param string $skeleton
     *
     * @throws IOException
     */
    private function writeSkeletonFile($skeleton) {
        $destination = $this->config->getDestination();
        if (file_exists($destination) && !$this->config->allowOverwrite()) {
            throw new IOException('A PHIVE configuration file already exists. Use the \'-force\' switch to overwrite it.');
        }
        file_put_contents($this->config->getDestination(), $skeleton);
    }

}
