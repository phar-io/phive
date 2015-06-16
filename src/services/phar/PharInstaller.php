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
         * @param PharFile $phar
         * @param string   $destination
         * @param bool     $copy
         */
        public function install(PharFile $phar, $destination, $copy) {
            file_put_contents($this->pharDirectory . '/' . $phar->getFilename(), $phar->getContent());

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
         * @param PharFile $phar
         * @param string   $destination
         */
        private function copy(PharFile $phar, $destination) {
            copy($this->pharDirectory . '/' . $phar->getFilename(), $destination);
            chmod($destination, 0755);
        }

        /**
         * @param PharFile $phar
         * @param string   $destination
         */
        private function link(PharFile $phar, $destination) {
            symlink($this->pharDirectory . '/' . $phar->getFilename(), $destination);
        }

    }

}

