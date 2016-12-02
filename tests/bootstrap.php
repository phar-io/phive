<?php

require __DIR__ . '/../src/autoload.php';
require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/autoload.php';

if (!extension_loaded('gnupg')) {
    class_alias(\PharIo\Phive\GnuPG::class, '\Gnupg');
}
