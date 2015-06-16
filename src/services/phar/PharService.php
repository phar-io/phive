<?php
namespace TheSeer\Phive {

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
         * @return PharFile
         * @throws DownloadFailedException
         */
        public function download(Url $pharUrl) {
            return $this->downloader->getFile($pharUrl);
        }

        /**
         * @param PharFile $phar
         * @param string   $destination
         * @param bool     $makeCopy
         */
        public function install(PharFile $phar, $destination, $makeCopy = false) {
            $this->installer->install($phar, $destination, $makeCopy);
        }

    }

}

