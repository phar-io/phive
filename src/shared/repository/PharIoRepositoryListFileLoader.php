<?php
namespace PharIo\Phive {

    class PharIoRepositoryListFileLoader {

        /**
         * @var Url
         */
        private $sourceUrl;

        /**
         * @var string
         */
        private $filename;

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
         * @param string         $filename
         * @param FileDownloader $fileDownloader
         * @param Output         $output
         */
        public function __construct(
            Url $sourceUrl,
            $filename,
            FileDownloader $fileDownloader,
            Output $output
        ) {
            $this->sourceUrl = $sourceUrl;
            $this->filename = $filename;
            $this->fileDownloader = $fileDownloader;
            $this->output = $output;
        }

        /**
         * @return string
         */
        public function load() {
            if (!file_exists($this->filename)) {
                $this->downloadFromSource();
            }
            return $this->filename;
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

}

