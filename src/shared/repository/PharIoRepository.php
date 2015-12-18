<?php
namespace PharIo\Phive {

    class PharIoRepository extends XmlRepository {

        /**
         * @param Url $repositoryUrl
         * @todo replace this with properly downloading the repo-XML beforehand
         */
        public function __construct(Url $repositoryUrl)
        {
            $repositoryXml = file_get_contents($repositoryUrl, false);
            $filename = tempnam('/tmp', 'repo_');
            file_put_contents($filename, $repositoryXml);
            parent::__construct($filename);
        }

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
                        new Url($releaseNode->getAttribute('url')),
                        $this->getHash($releaseNode)
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

        /**
         * @param \DOMElement $releaseNode
         *
         * @return Sha1Hash|Sha256Hash
         * @throws InvalidHashException
         */
        private function getHash(\DOMElement $releaseNode) {
            /** @var \DOMElement $hashNode */
            $hashNode = $releaseNode->getElementsByTagName('hash')->item(0);
            $type = $hashNode->getAttribute('type');
            switch ($type) {
                case 'sha-1':
                    return new Sha1Hash($hashNode->getAttribute('value'));
                case 'sha-256':
                    return new Sha256Hash($hashNode->getAttribute('value'));
            }
            throw new InvalidHashException(sprintf('Unsupported Hash Type %s', $type));
        }

    }

}

