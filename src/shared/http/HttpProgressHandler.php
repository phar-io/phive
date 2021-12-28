<?php declare(strict_types=1);
/*
 * This file is part of Phive.
 *
 * Copyright (c) Arne Blankerts <arne@blankerts.de>, Sebastian Heuer <sebastian@phpeople.de> and contributors
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */
namespace PharIo\Phive;

interface HttpProgressHandler {
    public function start(Url $url): void;

    public function finished(): void;

    /**
     * Method is called with updates from transfers (upload/download)
     * Return false to signal the http client to abort the transfer, true to continue.
     */
    public function handleUpdate(HttpProgressUpdate $update): bool;
}
