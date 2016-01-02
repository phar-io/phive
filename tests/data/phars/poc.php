<?php
require __DIR__ . '/../../../src/autoload.php';
$factory = new \PharIo\Phive\Factory();

$sigService = $factory->getSignatureService();
$keyService = $factory->getKeyService();

$phar = file_get_contents(__DIR__ . '/phploc-2.0.6.phar');
$sig = file_get_contents(__DIR__ . '/phploc-2.0.6.phar.asc');

try {
    $result = $sigService->verify($phar, $sig);
} catch (\PharIo\Phive\VerificationFailedException $e) {
    echo "signature verification failed: " . $e->getMessage() . "\n";
    exit(1);
}

$fingerprint = $result->getFingerprint();

echo "verifying signature \n";

if (!$result->wasVerificationSuccessful() && !$result->isKnownKey()) {
    echo "downloading and importing key $fingerprint \n";

    try {
        $keyService->importKey($keyService->downloadKey($fingerprint));
    } catch (InvalidArgumentException $e) {
        echo "download failed \n";
        exit(1);
    }

    echo "retrying verification \n";
    try {
        $result = $sigService->verify($phar, $sig);
    } catch (\PharIo\Phive\VerificationFailedException $e) {
        echo "signature verification failed: " . $e->getMessage() . "\n";
        exit(1);
    }
}

if (!$result->wasVerificationSuccessful()) {
    echo "signature is NOT vaild \n";
    exit(1);
}

echo "signature is valid \n";

