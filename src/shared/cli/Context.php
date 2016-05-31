<?php
namespace PharIo\Phive\Cli;

interface Context {

    /**
     * @return bool
     */
    public function canContinue();

    /**
     * @param $option
     *
     * @return bool
     */
    public function knowsOption($option);

    /**
     * @param $option
     *
     * @return bool
     */
    public function requiresValue($option);

    /**
     * @param $char
     *
     * @return string
     */
    public function getOptionForChar($char);

    /**
     * @return bool
     */
    public function acceptsArguments();

    /**
     * @param $arg
     */
    public function addArgument($arg);

    /**
     * @param string $option
     * @param string $value
     */
    public function setOption($option, $value);

    /**
     * @return Options
     */
    public function getOptions();

}
