<?php declare(strict_types = 1);
namespace PharIo\Phive;

interface HttpProgressHandler {
    public function start(Url $url): void;

    public function finished(): void;

    /**
     * Method is called with updates from transfers (upload/download)
     * Return false to signal the http client to abort the transfer, true to continue
     */
    public function handleUpdate(HttpProgressUpdate $update): bool;
}
