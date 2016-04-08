<?php
namespace PharIo\Phive\Cli;

interface Input {

    /**
     * @param string $message
     * @param bool $default
     *
     * @return bool
     */
    public function confirm($message, $default = true);

}
