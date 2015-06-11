<?php
namespace TheSeer\Phive {

    class PharInstaller {

        /**
         * @var string
         */
        private $homeDirectory = '';

        /**
         * @var string
         */
        private $workingDirectory = '';

        /**
         * @param string $homeDirectory
         * @param string $workingDirectory
         */
        public function __construct($homeDirectory, $workingDirectory) {
            $this->homeDirectory = $homeDirectory;
            $this->workingDirectory = $workingDirectory;
        }

        /**
         * @param PharFile $phar
         * @param bool     $copy
         */
        public function install(PharFile $phar, $copy) {
            $destination = $this->homeDirectory . '/phars/' . $phar->getFilename();
            if ($copy) {
                $destination = $this->workingDirectory . '/vendor/' . $phar->getFilename();
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
            $link = $this->workingDirectory . '/vendor/' . $phar->getFilename();
            if (file_exists($link)) {
                unlink($link);
            }
            symlink($this->homeDirectory . '/phars/' . $phar->getFilename(), $link);
        }

    }

}

