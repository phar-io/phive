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

use PharIo\FileSystem\Directory;
use PharIo\Phive\Cli\Options;

class TargetDirectoryLocator {
    /** @var Config */
    private $config;

    /** @var PhiveXmlConfig */
    private $phiveXmlConfig;

    /** @var Options */
    private $cliOptions;

    public function __construct(Config $config, PhiveXmlConfig $phiveXmlConfig, Options $cliOptions) {
        $this->config         = $config;
        $this->phiveXmlConfig = $phiveXmlConfig;
        $this->cliOptions     = $cliOptions;
    }

    /**
     * @throws Cli\CommandOptionsException
     * @throws ConfigException
     */
    public function getTargetDirectory(): Directory {
        if ($this->cliOptions->hasOption('target')) {
            $path = $this->cliOptions->getOption('target');

            if ($path[0] === DIRECTORY_SEPARATOR || preg_match('~\A[A-Z]:(?![^/\\\\])~i', $path) > 0) {
                return new Directory($path);
            }

            return $this->config->getWorkingDirectory()->child($path);
        }

        if ($this->phiveXmlConfig->hasTargetDirectory()) {
            return $this->phiveXmlConfig->getTargetDirectory();
        }

        return $this->config->getToolsDirectory();
    }
}
