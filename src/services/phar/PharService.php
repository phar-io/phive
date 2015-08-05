<?php
namespace PharIo\Phive {

    class PharService {

        /**
         * @var PharDownloader
         */
        private $downloader;

        /**
         * @var PharInstaller
         */
        private $installer;

        /**
         * @param PharDownloader $downloader
         * @param PharInstaller  $installer
         */
        public function __construct(PharDownloader $downloader, PharInstaller $installer) {
            $this->downloader = $downloader;
            $this->installer = $installer;
        }

        /**
         * @param Url $pharUrl
         *
         * @return File
         * @throws DownloadFailedException
         */
        public function download(Url $pharUrl) {
            return $this->downloader->download($pharUrl);
        }

        /**
         * @param File $phar
         * @param string   $destination
         * @param bool     $makeCopy
         */
        public function install(File $phar, $destination, $makeCopy = false) {
            $this->installer->install($phar, $destination, $makeCopy);
        }

    }

}

