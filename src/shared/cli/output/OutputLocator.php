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
namespace PharIo\Phive\Cli;

use PharIo\Phive\Environment;

class OutputLocator {
    /** @var OutputFactory */
    private $factory;

    public function __construct(OutputFactory $factory) {
        $this->factory = $factory;
    }

    public function getOutput(Environment $environment, bool $printProgressUpdates): Output {
        if ($environment->supportsColoredOutput()) {
            return $this->factory->getColoredConsoleOutput($printProgressUpdates);
        }

        return $this->factory->getConsoleOutput($printProgressUpdates);
    }
}
