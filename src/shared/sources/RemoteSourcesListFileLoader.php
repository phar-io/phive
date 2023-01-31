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

use DateTimeImmutable;
use PharIo\FileSystem\Filename;

class RemoteSourcesListFileLoader implements SourcesListFileLoader {
    /** @var Url */
    private $sourceUrl;

    /** @var Filename */
    private $filename;

    /** @var FileDownloader */
    private $fileDownloader;

    /** @var Cli\Output */
    private $output;

    /** @var DateTimeImmutable */
    private $maxAge;

    public function __construct(
        Url $sourceUrl,
        Filename $filename,
        FileDownloader $fileDownloader,
        Cli\Output $output,
        DateTimeImmutable $maxAge
    ) {
        $this->sourceUrl      = $sourceUrl;
        $this->filename       = $filename;
        $this->fileDownloader = $fileDownloader;
        $this->output         = $output;
        $this->maxAge         = $maxAge;
    }

    public function load(): SourcesList {
        if (!$this->filename->exists() || $this->filename->isOlderThan($this->maxAge)) {
            $this->downloadFromSource();
        }

        return new SourcesList(
            new XmlFile(
                $this->filename,
                'https://phar.io/repository-list',
                'repositories'
            )
        );
    }

    /**
     * @throws DownloadFailedException
     */
    public function downloadFromSource(): void {
        $this->output->writeInfo('Fetching repository list');
        $file = $this->fileDownloader->download($this->sourceUrl);
        $file->saveAs($this->filename);
    }
}
