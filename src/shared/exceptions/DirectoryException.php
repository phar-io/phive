<?php
namespace PharIo\Phive;

class DirectoryException extends \Exception {

    const InvalidMode = 1;
    const CreateFailed = 2;
    const ChmodFailed = 3;
    const InvalidType = 4;

}
