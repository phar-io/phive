<?php
namespace PharIo\Phive {

    class PharIoRepositoryFactory {

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
         * @param Url $url
         *
         * @return PharIoRepository
         * @throws DownloadFailedException
         */
        public function getRepository(Url $url) {
            $repositoryXml = $this->downloader->download($url);
            $filename = tempnam('/tmp', 'repo_');
            file_put_contents($filename, $repositoryXml);
            return new PharIoRepository($repositoryXml->getFilename());
        }

    }
}
