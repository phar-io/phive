<?php
require __DIR__ . '/../../vendor/autoload.php';
require __DIR__ . '/RegressionTestBootstrap.php';
require __DIR__ . '/RegressionTestCase.php';

(new \PharIo\Phive\RegressionTestBootstrap())->run();
