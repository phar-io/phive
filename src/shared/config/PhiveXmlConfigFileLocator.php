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

    public function getFile(): Filename {
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
