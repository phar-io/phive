<?php
namespace TheSeer\Phive {

    class Config {

        /**
         * @var Environment
         */
        private $environment;

        /**
         * @param Environment $environment
         */
        public function __construct(Environment $environment) {
            $this->environment = $environment;
        }

        /**
         * @return string
         */
        public function getHomeDirectory() {
            return $this->environment->getHomeDirectory() . '/.PHIVE';
        }

    }

}

