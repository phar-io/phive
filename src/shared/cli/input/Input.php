<?php
namespace PharIo\Phive\Cli;

interface Input {

    /**
     * @param string $message
     *
     * @return bool
     */
    public function confirm($message);

}
