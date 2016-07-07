<?php
namespace PharIo\Phive;

class ExecutorException extends \Exception implements Exception {

    const NotFound = 1;
    const NotExecutable = 2;
}
