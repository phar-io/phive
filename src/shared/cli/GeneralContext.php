<?php
namespace PharIo\Phive\Cli;

abstract class GeneralContext implements Context {

    /**
     * @var Options
     */
    private $options;

    /**
     * InstallContext constructor.
     */
    public function __construct() {
        $this->options = new Options();
    }

    /**
     * @param $arg
     */
    public function addArgument($arg) {
        $this->options->addArgument($arg);
    }

    /**
     * @param string $option
     * @param mixed  $value
     */
    public function setOption($option, $value) {
        $this->ensureNotConflicting($option);
        $this->options->setOption($option, $value);
    }

    /**
     * @return Options
     */
    public function getOptions() {
        return $this->options;
    }

    /**
     * @return bool
     */
    public function canContinue() {
        return true;
    }

    /**
     * @param $option
     *
     * @return bool
     */
    public function knowsOption($option) {
        return array_key_exists($option, $this->getKnownOptions());
    }

    /**
     * @param string $option
     *
     * @return bool
     */
    public function requiresValue($option) {
        return false;
    }

    public function hasOptionForChar($char) {
        if (!is_string($char) || strlen($char) !== 1) {
            throw new ContextException('short option must be a string of length 1');
        }
        return in_array($char, $this->getKnownOptions(), true);
    }

    public function getOptionForChar($char) {
        if (!$this->hasOptionForChar($char)) {
            throw new ContextException('No short option with this char');
        }
        return array_search($char, $this->getKnownOptions(), true);
    }

    /**
     * @return bool
     */
    public function acceptsArguments() {
        return true;
    }

    /**
     * Return Options array
     *
     * Format: (key == name, value = short-char or false, e.g. ['long' => 'l', 'other' => false])
     * Return empty array if no options are supported
     *
     * @return array
     */
    protected function getKnownOptions() {
        return [];
    }

    /**
     * Return conflicting pairs of options
     *
     * Format: Array of pairs (e.g. [ ['a' => 'b'], ['a' => 'c'] ])
     * Lookup is performed both ways
     * Return empty array if no options are conflicting
     *
     * @return array
     */
    protected function getConflictingOptions() {
        return [];
    }

    private function ensureNotConflicting($option) {
        $list = $this->getConflictingOptions();
        foreach ($list as $pair) {
            foreach ($pair as $opt1 => $opt2) {
                if ($option !== $opt1 && $option !== $opt2) {
                    continue;
                }
                if (($option === $opt1 && $this->options->hasOption($opt2)) ||
                    ($option === $opt2 && $this->options->hasOption($opt1))
                ) {
                    throw new ContextException(
                        sprintf("Options '%s' and '%s' cannot be combined", $opt1, $opt2),
                        ContextException::ConflictingOptions
                    );
                }
            }
        }
    }
}
