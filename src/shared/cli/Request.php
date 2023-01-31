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

use function count;
use function sprintf;
use function strlen;
use function substr;
use OutOfBoundsException;

class Request {
    /** @var array */
    private $argv;

    /** @var int */
    private $pos = 0;

    /** @var int */
    private $count;

    /** @var Options */
    private $options;

    public function __construct(array $argv) {
        $this->argv    = $argv;
        $this->count   = count($argv) - 1;
        $this->options = new Options();
    }

    public function parse(Context $context): Options {
        while ($this->hasNext()) {
            $arg = $this->getNext();

            if ($arg[0] === '-') {
                if ($arg[1] === '-') {
                    $this->handleLongOption($context, substr($arg, 2));
                } else {
                    $len = strlen($arg) - 1;

                    for ($t = 1; $t <= $len; $t++) {
                        $this->handleShortOption($context, $arg[$t], ($t === $len));
                    }
                }
            } else {
                $this->handleArgument($context, $arg);
            }

            if (!$context->canContinue()) {
                break;
            }
        }

        $this->options = $context->getOptions()->mergeOptions($this->options);

        return $this->getOptions();
    }

    public function getOptions(): Options {
        return $this->options;
    }

    private function getNext(): string {
        if (!$this->hasNext()) {
            throw new OutOfBoundsException('No more parameters');
        }
        $this->pos++;

        return $this->argv[$this->pos];
    }

    private function hasNext(): bool {
        return $this->pos < $this->count;
    }

    private function handleLongOption(Context $context, string $option): void {
        if (!$context->knowsOption($option)) {
            throw new RequestException(
                sprintf('Unknown option: %s', $option),
                RequestException::InvalidOption
            );
        }

        if ($context->requiresValue($option)) {
            if (!$this->hasNext()) {
                throw new RequestException(
                    sprintf('Option %s requires a value - none given', $option),
                    RequestException::ValueRequired
                );
            }
            $value = $this->getNext();

            if ($value[0] === '-') {
                throw new RequestException(
                    sprintf('Option %s requires a value - none given', $option),
                    RequestException::ValueRequired
                );
            }
        } else {
            $value = true;
        }

        $this->setOption($context, $option, $value);
    }

    private function handleShortOption(Context $context, string $char, bool $isLast): void {
        if (!$context->hasOptionForChar($char)) {
            throw new RequestException(
                sprintf('Unknown option: %s', $char),
                RequestException::InvalidOption
            );
        }
        $option = $context->getOptionForChar($char);

        if ($context->requiresValue($option)) {
            if (!$isLast || !$this->hasNext()) {
                throw new RequestException(
                    sprintf('Option %s requires a value - none given', $option),
                    RequestException::ValueRequired
                );
            }
            $value = $this->getNext();

            if ($value[0] === '-') {
                throw new RequestException(
                    sprintf('Option %s requires a value - none given', $option),
                    RequestException::ValueRequired
                );
            }
            $this->setOption($context, $option, $value);
        } else {
            $this->setOption($context, $option, true);
        }
    }

    private function handleArgument(Context $context, string $arg): void {
        if (!$context->acceptsArguments()) {
            throw new RequestException(
                'Unexpected argument ' . $arg,
                RequestException::UnexpectedArgument
            );
        }
        $context->addArgument($arg);
    }

    /**
     * @param string|true $value
     *
     * @throws RequestException
     */
    private function setOption(Context $context, string $option, $value): void {
        try {
            $context->setOption($option, $value);
        } catch (ContextException $e) {
            throw new RequestException(
                $e->getMessage(),
                RequestException::InvalidOption
            );
        }
    }
}
