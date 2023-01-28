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

use const STDIN;
use function fgets;
use function in_array;
use function rtrim;
use function sprintf;
use function strtolower;

class ConsoleInput implements Input {
    /** @var Output */
    private $output;

    /** @var false|resource */
    private $inputStream;

    /**
     * @param false|resource $inputStreamHandle
     */
    public function __construct(Output $output, $inputStreamHandle = STDIN) {
        $this->output      = $output;
        $this->inputStream = $inputStreamHandle;
    }

    public function confirm(string $message, bool $default = true): bool {
        $yesOption = $default === true ? 'Y' : 'y';
        $noOption  = $default === false ? 'N' : 'n';

        if ($this->inputStream === false) {
            throw new RunnerException('Needs tty to be able to confirm');
        }

        do {
            $this->output->writeText(rtrim($message) . sprintf(' [%s|%s] ', $yesOption, $noOption));
            $input = fgets($this->inputStream);

            if ($input === false) {
                throw new RunnerException('Needs tty to be able to confirm');
            }

            $response = strtolower(rtrim($input));
        } while (!in_array($response, ['y', 'n', ''], true));

        if ($response === '') {
            return $default;
        }

        return $response === 'y';
    }
}
