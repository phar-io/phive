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

use function file_get_contents;
use function fwrite;
use function stream_get_meta_data;
use function tmpfile;

class LocalSslCertificate {
    /** @var string */
    private $hostname;

    /** @var resource */
    private $temporaryCertificateFile;

    public function __construct(string $hostname, string $sourceFile) {
        $this->hostname = $hostname;
        $this->createTemporaryCertificateFile($sourceFile);
    }

    public function getHostname(): string {
        return $this->hostname;
    }

    public function getCertificateFile(): string {
        return stream_get_meta_data($this->temporaryCertificateFile)['uri'];
    }

    /**
     * @param string $sourceFile
     */
    private function createTemporaryCertificateFile($sourceFile): void {
        $this->temporaryCertificateFile = tmpfile();
        fwrite($this->temporaryCertificateFile, file_get_contents($sourceFile));
    }
}
