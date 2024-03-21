<?php declare(strict_types=1);
/*
 * This file is part of Phive.
 *
 * Copyright (c) Arne Blankerts <arne@blankerts.de>, Sebastian Heuer <sebastian@phpeople.de> and contributors
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */
namespace PharIo\Phive;

use function file_get_contents;
use function file_put_contents;
use function sprintf;
use function str_replace;
use DateTimeImmutable;
use PharIo\FileSystem\Filename;
use PharIo\Phive\Cli\Output;

class SkelCommand implements Cli\Command {
    /** @var SkelCommandConfig */
    private $config;

    /** @var PhiveVersion */
    private $version;

    /** @var DateTimeImmutable */
    private $now;

    /** @var Output */
    private $output;

    public function __construct(SkelCommandConfig $config, Output $output, PhiveVersion $version, ?DateTimeImmutable $now = null) {
        $this->config  = $config;
        $this->version = $version;

        if ($now === null) {
            $now = new DateTimeImmutable();
        }
        $this->now    = $now;
        $this->output = $output;
    }

    public function execute(): void {
        $skeleton = file_get_contents($this->config->getTemplateFilename());
        $skeleton = $this->replacePlaceholder($skeleton, '%%VERSION%%', $this->version->getVersion());
        $skeleton = $this->replacePlaceholder($skeleton, '%%DATE%%', $this->now->format('Y-m-d H:i:sO'));
        $target   = $this->writeSkeletonFile($skeleton);

        $this->output->writeInfo(
            sprintf('Skeleton file created in %s.', $target->asString())
        );
    }

    private function replacePlaceholder(string $content, string $placeholder, string $replacement): string {
        return str_replace($placeholder, $replacement, $content);
    }

    /**
     * @throws IOException
     */
    private function writeSkeletonFile(string $skeleton): Filename {
        $destination = new Filename($this->config->getDestination());

        if ($destination->exists() && !$this->config->allowOverwrite()) {
            throw new IOException(
                'A PHIVE configuration file already exists. Use the "--force" switch to overwrite it.'
            );
        }

        $destination->getDirectory()->ensureExists();

        file_put_contents($destination->asString(), $skeleton);

        return $destination;
    }
}
