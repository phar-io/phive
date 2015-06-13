<?php
namespace TheSeer\Phive {

    class PharInstaller {

        /**
         * @var Directory
         */
        private $pharDirectory = '';

        /**
         * @var string
         */
        private $workingDirectory = '';

        /**
         * @param Directory $pharDirectory
         * @param Directory $workingDirectory
         */
        public function __construct(Directory $pharDirectory, Directory $workingDirectory) {
            $this->pharDirectory = $pharDirectory;
            $this->workingDirectory = $workingDirectory;
        }

        /**
         * @param PharFile $phar
         * @param bool     $copy
         */
        public function install(PharFile $phar, $copy) {
            $destination = $this->pharDirectory . '/' . $phar->getFilename();
            if ($copy) {
                $destination = $this->workingDirectory . '/' . $phar->getFilename();
            }
            $phar->saveAs($destination);
            if (!$copy) {
                $this->link($phar);
            }
        }

        /**
         * @param PharFile $phar
         */
        private function link(PharFile $phar) {
            $link = $this->workingDirectory . '/' . $phar->getFilename();
            if (file_exists($link)) {
                unlink($link);
            }
            symlink($this->pharDirectory . '/' . $phar->getFilename(), $link);
        }

    }

}

