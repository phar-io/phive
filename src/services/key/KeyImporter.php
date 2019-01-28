<?php declare(strict_types = 1);
namespace PharIo\Phive;

interface KeyImporter {
    public function importKey(string $key): KeyImportResult;
}
