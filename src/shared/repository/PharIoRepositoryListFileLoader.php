<?php
namespace PharIo\Phive {

    class PharIoRepositoryListFileLoader {

        const FILENAME = 'repositories.xml';

        /**
         * @var Url
         */
        private $sourceUrl;

        /**
         * @var FileDownloader
         */
        private $fileDownloader;

        /**
         * @var Output
         */
        private $output;

        /**
         * @param Url            $sourceUrl
         * @param FileDownloader $fileDownloader
         * @param Output         $output
         */
        public function __construct(Url $sourceUrl, FileDownloader $fileDownloader, Output $output) {
            $this->sourceUrl = $sourceUrl;
            $this->fileDownloader = $fileDownloader;
            $this->output = $output;
        }

        /**
         * @param Directory $directory
         *
         * @return string
         * @throws DownloadFailedException
         */
        public function load(Directory $directory) {
            $filename = $directory->file(self::FILENAME);
            if (!file_exists($filename)) {
                $this->output->writeInfo(sprintf('Downloading repository list from %s', $this->sourceUrl));
                $file = $this->fileDownloader->download($this->sourceUrl);
                $file->saveAs($filename);
            }
            return $filename;
        }


    }

}

