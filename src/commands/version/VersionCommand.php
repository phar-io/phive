<?php
namespace TheSeer\Phive {

    class VersionCommand implements CommandInterface {

        /**
         * @var Version
         */
        private $version;

        /**
         * @param Version $version
         */
        public function __construct(Version $version) {
            $this->version = $version;
        }

        public function execute() {
            echo $this->version->getVersionString() . "\n\n";
        }

    }

}
