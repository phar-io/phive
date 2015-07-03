<?php
namespace TheSeer\Phive {

    class PharInstaller {

        /**
         * @var Directory
         */
        private $pharDirectory = '';

        /**
         * @var Logger
         */
        private $logger;

        /**
         * @param Directory $pharDirectory
         * @param Logger    $logger
         */
        public function __construct(Directory $pharDirectory, Logger $logger) {
            $this->pharDirectory = $pharDirectory;
            $this->logger = $logger;
        }

        /**
         * @param File $phar
         * @param string   $destination
         * @param bool     $copy
         */
        public function install(File $phar, $destination, $copy) {
            if (file_exists($destination)) {
                unlink($destination);
            }
            if ($copy) {
                $this->copy($phar, $destination);
            } else {
                $this->link($phar, $destination);
            }
        }

        /**
         * @param File $phar
         * @param string   $destination
         */
        private function copy(File $phar, $destination) {
            $this->logger->logInfo(sprintf('Copying %s to %s', $phar->getFilename(), $destination));
            copy($this->pharDirectory . DIRECTORY_SEPARATOR . $phar->getFilename(), $destination);
            chmod($destination, 0755);
        }

        /**
         * @param File $phar
         * @param string   $destination
         */
        private function link(File $phar, $destination) {
            $this->logger->logInfo(sprintf('Symlinking %s to %s', $phar->getFilename(), $destination));
            symlink($this->pharDirectory . DIRECTORY_SEPARATOR . $phar->getFilename(), $destination);
        }

    }

}

