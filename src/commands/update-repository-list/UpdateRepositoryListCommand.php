<?php
namespace PharIo\Phive {

    class UpdateRepositoryListCommand {

        /**
         * @var PharIoRepositoryListFileLoader
         */
        private $loader;

        /**
         * @param PharIoRepositoryListFileLoader $loader
         */
        public function __construct(PharIoRepositoryListFileLoader $loader) {
            $this->loader = $loader;
        }

        /**
         *
         */
        public function execute() {
            $this->loader->downloadFromSource();
        }


    }

}