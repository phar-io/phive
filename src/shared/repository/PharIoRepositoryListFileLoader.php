<?php
namespace PharIo\Phive {

    class PharIoRepositoryListFileLoader {

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
         * @var Output
         */
        private $output;

        /**
         * @param Url            $sourceUrl
         * @param Filename       $filename
         * @param FileDownloader $fileDownloader
         * @param Output         $output
         */
        public function __construct(
            Url $sourceUrl,
            Filename $filename,
            FileDownloader $fileDownloader,
            Output $output
        ) {
            $this->sourceUrl = $sourceUrl;
            $this->filename = $filename;
            $this->fileDownloader = $fileDownloader;
            $this->output = $output;
        }

        /**
         * @return Filename
         */
        public function load() {
            if (!$this->filename->exists()) {
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

