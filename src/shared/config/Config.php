<?php
namespace PharIo\Phive {

    class Config {

        /**
         * @var Environment
         */
        private $environment;

        /**
         * @var PhiveXmlConfig
         */
        private $phiveXmlConfig;

        /**
         * @param Environment    $environment
         * @param PhiveXmlConfig $phiveXmlConfig
         */
        public function __construct(Environment $environment, PhiveXmlConfig $phiveXmlConfig) {
            $this->environment = $environment;
            $this->phiveXmlConfig = $phiveXmlConfig;
        }

        /**
         * @return Directory
         */
        public function getHomeDirectory() {
            return $this->environment->getHomeDirectory()->child('.phive');
        }

        /**
         * @return Directory
         */
        public function getWorkingDirectory() {
            return $this->environment->getWorkingDirectory();
        }

        /**
         * @return string
         */
        public function getGPGBinaryPath() {
            return '/usr/bin/gpg';
        }

        /**
         * @return Url
         */
        public function getRepositoryListUrl() {
            return new Url('https://phar.io/data/repositories.xml');
        }

        /**
         * @return array
         */
        public function getPhars() {
            return $this->phiveXmlConfig->getPhars();
        }

    }

}

