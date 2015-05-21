<?php
namespace TheSeer\Phive {

    class PharFile {

        private $content;

        public function __construct($content) {
            $this->content = $content;
        }

        public function saveAs($destination) {
            file_put_contents($destination, $this->content);
            chmod($destination, 0755);
        }
    }

}
