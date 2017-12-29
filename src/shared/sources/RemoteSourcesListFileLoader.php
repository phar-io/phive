<?php
namespace PharIo\Phive;

use PharIo\FileSystem\Filename;

class RemoteSourcesListFileLoader implements SourcesListFileLoader {

    /**
     * @var Url
     */
    private $sourceUrl;

    /**
     * @var Filename
     */
    private $filename;

    /**
     * @var FileDownloader
     */
    private $fileDownloader;

    /**
     * @var Cli\Output
     */
    private $output;

    /**
     * @var \DateTimeImmutable
     */
    private $maxAge;

    /**
     * @param Url                $sourceUrl
     * @param Filename           $filename
     * @param FileDownloader     $fileDownloader
     * @param Cli\Output         $output
     * @param \DateTimeImmutable $maxAge
     */
    public function __construct(
        Url $sourceUrl,
        Filename $filename,
        FileDownloader $fileDownloader,
        Cli\Output $output,
        \DateTimeImmutable $maxAge
    ) {
        $this->sourceUrl = $sourceUrl;
        $this->filename = $filename;
        $this->fileDownloader = $fileDownloader;
        $this->output = $output;
        $this->maxAge = $maxAge;
    }

    /**
     * @return SourcesList
     */
    public function load() {
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
    public function downloadFromSource() {
        $this->output->writeInfo('Fetching respository list');
        $file = $this->fileDownloader->download($this->sourceUrl);
        $file->saveAs($this->filename);
    }

}
