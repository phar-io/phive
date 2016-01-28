<?php
namespace PharIo\Phive;

class ErrorException extends \ErrorException {

    /**
     * @var array
     */
    private $context;

    public function __construct($message, $code, $severity, $filename, $lineno, array $context, \Exception $previous = null) {
        parent::__construct($message, $code, $severity, $filename, $lineno, $previous);
        $this->context = $context;
    }

    /**
     * @return array
     */
    public function getContext() {
        return $this->context;
    }

}
