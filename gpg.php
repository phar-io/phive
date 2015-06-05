<?php
require __DIR__ . '/src/autoload.php';
$factory = new \TheSeer\Phive\Factory();

$sigService = $factory->getSignatureService();
$keyService = $factory->getKeyService();

$phar =  file_get_contents(__DIR__ . '/tests/data/phars/phploc-2.0.6.phar');
$sig = file_get_contents(__DIR__ . '/tests/data/phars/phploc-2.0.6.phar.asc');

try {
    $result = $sigService->verify($phar, $sig);
} catch (\TheSeer\Phive\VerificationFailedException $e) {
    echo "signature verification failed: " . $e->getMessage() . "\n";
    exit(1);
}

$fingerprint = $result->getFingerprint();

echo "verifying signature... \n";

if (!$result->isKnownKey()) {
    echo "downloading and importing key $fingerprint \n";
    try {
        $keyService->importKey($keyService->downloadKey($fingerprint));
    } catch (InvalidArgumentException $e) {
        echo "download failed \n";
        exit(1);
    }
}

try {
    $result = $sigService->verify($phar, $sig);
} catch (\TheSeer\Phive\VerificationFailedException $e) {
    echo "signature verification failed: " . $e->getMessage() . "\n";
    exit(1);
}

echo "signature is valid \n";

