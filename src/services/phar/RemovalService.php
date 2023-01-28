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
use PharIo\Phive\Cli\Output;

class RemovalService {
    /** @var Environment */
    private $environment;

    /** @var Output */
    private $output;

    public function __construct(Environment $environment, Output $output) {
        $this->environment = $environment;
        $this->output      = $output;
    }

    public function remove(Filename $filename): void {
        if ($this->environment instanceof WindowsEnvironment) {
            $filename = new Filename($filename->asString() . '.bat');
        }

        if (!$filename->exists()) {
            $this->output->writeWarning(
                sprintf('Cannot delete file "%s": File not found.', $filename->asString())
            );

            return;
        }

        $filename->delete();
    }
}
