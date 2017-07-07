<?php
namespace PharIo\Phive\Cli;

class Request {

    /**
     * @var array
     */
    private $argv;

    /**
     * @var int
     */
    private $pos = 0;

    /**
     * @var int
     */
    private $count;

    /**
     * @var Options
     */
    private $options;

    /**
     * @param array $argv
     */
    public function __construct(array $argv) {
        $this->argv = $argv;
        $this->count = count($argv) - 1;
        $this->options = new Options();
    }

    public function parse(Context $context) {
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

    /**
     * @return Options
     */
    public function getOptions() {
        return $this->options;
    }

    /**
     * @return string
     */
    private function getNext() {
        if (!$this->hasNext()) {
            throw new \OutOfBoundsException('No more parameters');
        }
        $this->pos++;

        return $this->argv[$this->pos];
    }

    /**
     * @return bool
     */
    private function hasNext() {
        return $this->pos < $this->count;
    }

    private function handleLongOption(Context $context, $option) {
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
            if ($value[0] == '-') {
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

    private function handleShortOption(Context $context, $char, $isLast) {
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
            if ($value[0] == '-') {
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

    private function handleArgument(Context $context, $arg) {
        if (!$context->acceptsArguments()) {
            throw new RequestException(
                'Unexpected argument ' . $arg,
                RequestException::UnexpectedArgument
            );
        }
        $context->addArgument($arg);
    }

    private function setOption(Context $context, $option, $value) {
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
