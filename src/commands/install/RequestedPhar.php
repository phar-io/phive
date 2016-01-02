<?php
namespace PharIo\Phive {

    class RequestedPhar {

        /**
         * @var PharAlias
         */
        private $alias;

        /**
         * @var Url
         */
        private $url;

        /**
         * @param PharAlias $alias
         *
         * @return RequestedPhar
         */
        public static function fromAlias(PharAlias $alias) {
            return new self($alias);
        }

        /**
         * @param Url $url
         *
         * @return RequestedPhar
         */
        public static function fromUrl(Url $url) {
            return new self(null, $url);
        }

        /**
         * @return bool
         */
        public function isAlias() {
            return null !== $this->alias;
        }

        /**
         * @return PharAlias
         */
        public function getAlias() {
            return $this->alias;
        }

        /**
         * @return Url
         */
        public function getPharUrl() {
            return $this->url;
        }

        /**
         * @param PharAlias|null $alias
         * @param Url|null       $url
         */
        private function __construct(PharAlias $alias = null, Url $url = null) {
            $this->alias = $alias;
            $this->url = $url;
        }

    }

}
