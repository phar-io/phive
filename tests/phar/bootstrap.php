<?php
require __DIR__ . '/../../src/autoload.php';
require __DIR__ . '/PharTestBootstrap.php';
require __DIR__ . '/PharTestCase.php';

(new \PharIo\Phive\PharTestBootstrap())->run();