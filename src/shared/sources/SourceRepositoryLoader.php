<?php
namespace PharIo\Phive;

class SourceRepositoryLoader {

    /**
     * @var FileDownloader
     */
    private $downloader;

    /**
     * @param FileDownloader $downloader
     */
    public function __construct(FileDownloader $downloader) {
        $this->downloader = $downloader;
    }

    /**
     * @param Source $source
     *
     * @return SourceRepository
     *
     * @throws DownloadFailedException
     * @throws \RuntimeException
     */
    public function loadRepository(Source $source) {
        $dataFile = $this->downloader->download($source->getUrl());
        $filename = new Filename(tempnam(sys_get_temp_dir(), 'repo_'));
        $dataFile->saveAs($filename);

        switch($source->getType()) {
            case 'phar.io': {
                return new PharIoRepository(
                    new XmlFile(
                        $filename,
                        'https://phar.io/repository',
                        'repository'
                    )
                );
            }
            case 'github': {
                return new GithubRepository(
                    new JsonData(
                        $dataFile->getContent()
                    )
                );
            }

            default: {
                throw new \RuntimeException(
                    sprintf('Unexpected source type "%s"', $source->getType())
                );
            }
        }

    }

}
