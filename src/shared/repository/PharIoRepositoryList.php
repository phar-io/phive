<?php
namespace PharIo\Phive {

    class PharIoRepositoryList extends XmlRepository {

        /**
         * @param PharAlias $alias
         *
         * @return Url[]
         */
        public function getRepositoryUrls(PharAlias $alias) {
            $urls = [];
            $query = sprintf('//phar[@alias="%s"]/repository', $alias);
            foreach ($this->getXPath()->query($query) as $repositoryNode) {
                /** @var \DOMElement $repositoryNode */
                $urls[] = new Url($repositoryNode->getAttribute('url'));
            }
            return $urls;
        }

        /**
         * @return string
         */
        protected function getRootElementName() {
            return 'repositories';
        }

        /**
         * @return string
         */
        protected function getNamespace() {
            return '';
        }

    }

}

