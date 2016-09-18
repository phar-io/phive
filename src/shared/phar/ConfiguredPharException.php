<?php
namespace PharIo\Phive;

class ConfiguredPharException extends \Exception implements Exception {

    const NoLocation = 1;
    const NoUrl = 2;
}
