<?php
namespace PharIo\Phive {

    /**
     * Class Filename
     *
     * @package shared
     */
    class Filename {

        /**
         * @var string
         */
        private $name;

        /**
         * Filename constructor.
         *
         * @param string $name
         */
        public function __construct($name) {
            $this->ensureString($name);
            $this->name = $name;
        }

        /**
         * @param $name
         *
         * @throws \InvalidArgumentException
         */
        private function ensureString($name) {
            if (!is_string($name)) {
                throw new \InvalidArgumentException(
                    sprintf(
                        'String expected but "%s" received',
                        is_object($name) ? get_class($name) : gettype($name)
                    )
                );
            }
        }

        /**
         * @return string
         */
        public function __toString() {
            return $this->aSstring();
        }

        public function asString() {
            return $this->name;
        }

        /**
         * @return bool
         */
        public function exists() {
            return file_exists($this->name);
        }

    }

}
