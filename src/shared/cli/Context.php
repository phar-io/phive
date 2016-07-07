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
     * @param $char
     *
     * @return bool
     */
    public function hasOptionForChar($char);

    /**
     * @return bool
     */
    public function acceptsArguments();

    /**
     * @param string $arg
     */
    public function addArgument($arg);

    /**
     * @param string $option
     * @param mixed  $value
     */
    public function setOption($option, $value);

    /**
     * @return Options
     */
    public function getOptions();

}
