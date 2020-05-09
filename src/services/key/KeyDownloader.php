<?php declare(strict_types = 1);
namespace PharIo\Phive;

interface KeyDownloader {
    public function download(string $keyId): PublicKey;
}
