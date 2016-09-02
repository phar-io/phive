<?php
namespace PharIo\Phive;

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
     * @param Url            $sourceUrl
     * @param Filename       $filename
     * @param FileDownloader $fileDownloader
     * @param Cli\Output     $output
     */
    public function __construct(
        Url $sourceUrl,
        Filename $filename,
        FileDownloader $fileDownloader,
        Cli\Output $output
    ) {
        $this->sourceUrl = $sourceUrl;
        $this->filename = $filename;
        $this->fileDownloader = $fileDownloader;
        $this->output = $output;
    }

    /**
     * @return SourcesList
     */
    public function load() {
        if (!$this->filename->exists()) {
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
        $this->output->writeInfo(sprintf('Downloading repository list from %s', $this->sourceUrl));
        $file = $this->fileDownloader->download($this->sourceUrl);
        $file->saveAs($this->filename);
    }

}
