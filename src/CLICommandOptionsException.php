<?php
namespace PharIo\Phive {

    class CLICommandOptionsException extends \Exception {
        const NoSuchOption = 1;
        const InvalidArgumentIndex = 2;
    }

}
