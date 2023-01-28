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

use PharIo\FileSystem\Filename;

class OutdatedConfig {
    /** @var Cli\Options */
    private $options;

    public function __construct(Cli\Options $options) {
        $this->options = $options;
    }

    public function saveToFile(): bool {
        return $this->options->hasOption('output');
    }

    public function outputFilename(): Filename {
        if (!$this->saveToFile()) {
            throw new OutdatedConfigException('No output file name set');
        }

        return new Filename($this->options->getOption('output'));
    }

    public function wantsJson(): bool {
        return $this->options->hasOption('json');
    }

    public function wantsXml(): bool {
        return $this->options->hasOption('xml');
    }
}
