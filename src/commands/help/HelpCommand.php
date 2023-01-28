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
use function str_replace;

class HelpCommand implements Cli\Command {
    /** @var Environment */
    private $environment;

    /** @var Cli\Output */
    private $output;

    public function __construct(Environment $environment, Cli\Output $output) {
        $this->environment = $environment;
        $this->output      = $output;
    }

    public function execute(): void {
        $this->output->writeMarkdown(
            str_replace(
                '%phive',
                $this->environment->getPhiveCommandPath(),
                file_get_contents(__DIR__ . '/help.md')
            ) . "\n\n"
        );
    }
}
