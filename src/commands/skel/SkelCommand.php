<?php declare(strict_types = 1);
namespace PharIo\Phive;

class SkelCommand implements Cli\Command {

    /** @var SkelCommandConfig */
    private $config;

    /** @var PhiveVersion */
    private $version;

    /** @var \DateTimeImmutable */
    private $now;

    public function __construct(SkelCommandConfig $config, PhiveVersion $version, \DateTimeImmutable $now = null) {
        $this->config  = $config;
        $this->version = $version;

        if ($now === null) {
            $now = new \DateTimeImmutable();
        }
        $this->now = $now;
    }

    public function execute(): void {
        $skeleton = \file_get_contents($this->config->getTemplateFilename());
        $skeleton = $this->replacePlaceholder($skeleton, '%%VERSION%%', $this->version->getVersion());
        $skeleton = $this->replacePlaceholder($skeleton, '%%DATE%%', $this->now->format('Y-m-d H:i:sO'));
        $this->writeSkeletonFile($skeleton);
    }

    private function replacePlaceholder(string $content, string $placeholder, string $replacement): string {
        return \str_replace($placeholder, $replacement, $content);
    }

    /**
     * @throws IOException
     */
    private function writeSkeletonFile(string $skeleton): void {
        $destination = $this->config->getDestination();

        if (\file_exists($destination) && !$this->config->allowOverwrite()) {
            throw new IOException(
                'A PHIVE configuration file already exists. Use the "--force" switch to overwrite it.'
            );
        }
        \file_put_contents($this->config->getDestination(), $skeleton);
    }
}
