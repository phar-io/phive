<?php
namespace TheSeer\Phive {

    /**
     * Wrapper for CLI environment variables
     */
    class Environment {

        /**
         * @var array
         */
        private $server = [];

        /**
         * @param array $server
         */
        public function __construct(array $server) {
            $this->server = $server;
        }

        /**
         * @return bool
         */
        public function hasProxy() {
            return (array_key_exists('https_proxy', $this->server));
        }

        /**
         * @return string
         */
        public function getProxy() {
            if (!$this->hasProxy()) {
                throw new \BadMethodCallException('No proxy set in environment');
            }
            return $this->server['https_proxy'];
        }

    }

}

