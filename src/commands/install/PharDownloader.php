<?php
namespace TheSeer\Phive {

    class PharDownloader {

        public function getFile($url) {
            $contents = file_get_contents($url);
            return new PharFile($contents);
        }
    }

}
