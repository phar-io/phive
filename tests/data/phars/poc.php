<?php

$phar = file_get_contents(__DIR__ . '/phploc-2.0.6.phar');
$sig  = file_get_contents(__DIR__ . '/phploc-2.0.6.phar.asc');

putenv('GNUPGHOME=/tmp/phive');
$gpg = new Gnupg();
$gpg -> seterrormode(gnupg::ERROR_EXCEPTION);

$info = $gpg->verify($phar,$sig);
print_r($info);
print_r($gpg->keyinfo($info[0]['fingerprint']));

