<?php
namespace TheSeer\Phive {

    class PharInstaller {

        /**
         * @var Directory
         */
        private $pharDirectory = '';

        /**
         * @param Directory $pharDirectory
         */
        public function __construct(Directory $pharDirectory) {
            $this->pharDirectory = $pharDirectory;
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
            copy($this->pharDirectory . DIRECTORY_SEPARATOR . $phar->getFilename(), $destination);
            chmod($destination, 0755);
        }

        /**
         * @param File $phar
         * @param string   $destination
         */
        private function link(File $phar, $destination) {
            symlink($this->pharDirectory . DIRECTORY_SEPARATOR . $phar->getFilename(), $destination);
        }

    }

}

