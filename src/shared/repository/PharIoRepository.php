<?php
namespace PharIo\Phive {

    class PharIoRepository extends XmlRepository {

        /**
         * @param PharAlias $alias
         *
         * @return ReleaseCollection
         */
        public function getReleases(PharAlias $alias) {
            $releases = new ReleaseCollection();
            $query = sprintf('//phive:phar[@name="%s"]/phive:release', $alias);
            foreach ($this->getXPath()->query($query) as $releaseNode) {
                /** @var \DOMElement $releaseNode */
                $releases->add(
                    new Release(
                        new Version($releaseNode->getAttribute('version')),
                        new Url($releaseNode->getAttribute('url'))
                    )
                );
            }
            return $releases;
        }

        /**
         * @return string
         */
        protected function getRootElementName() {
            return 'repository';
        }

        /**
         * @return string
         */
        protected function getNamespace() {
            return 'https://phar.io/repository';
        }

    }

}

