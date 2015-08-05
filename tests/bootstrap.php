<?php

require __DIR__ . '/../src/autoload.php';

if (!extension_loaded('gnupg')) {
    class_alias(\PharIo\Phive\GnuPG::class, '\Gnupg');
}
