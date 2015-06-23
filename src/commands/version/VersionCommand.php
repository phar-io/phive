<?php
namespace TheSeer\Phive {

    class VersionCommand implements Command {

        /**
         * @var PhiveVersion
         */
        private $version;

        /**
         * @param PhiveVersion $version
         */
        public function __construct(PhiveVersion $version) {
            $this->version = $version;
        }

        public function execute() {
            echo $this->version->getVersionString() . "\n\n";
        }

    }

}
