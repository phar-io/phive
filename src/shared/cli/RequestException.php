<?php
namespace PharIo\Phive\Cli;

class RequestException extends \Exception {
    const UnexpectedArgument = 1;
    const InvalidOption = 2;
    const ValueRequired = 3;
}
