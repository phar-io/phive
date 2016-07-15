<?php
namespace PharIo\Phive\Cli;

use PharIo\Phive\Exception;

class ContextException extends \Exception implements Exception {

    const ConflictingOptions = 1;

}
