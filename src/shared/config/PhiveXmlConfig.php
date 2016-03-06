<?php
namespace PharIo\Phive;

class PhiveXmlConfig {

    /**
     * @var XmlFile
     */
    private $configFile;

    /**
     * PhiveXmlConfig constructor.
     *
     * @param XmlFile $configFile
     */
    public function __construct(XmlFile $configFile) {
        $this->configFile = $configFile;
    }

    /**
     * @param RequestedPhar $requestedPhar
     */
    public function addPhar(RequestedPhar $requestedPhar) {
        $name = (string)$requestedPhar->getAlias();
        if ($this->hasPharNode($name)) {
            $pharNode = $this->getPharNode($name);
        } else {
            $pharNode = $this->configFile->createElement('phar');
            $pharNode->setAttribute('name', $name);
            $this->configFile->addElement($pharNode);
        }
        $pharNode->setAttribute('version', $requestedPhar->getAlias()->getVersionConstraint()->asString());
        $this->configFile->save();
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    private function hasPharNode($name) {
        return $this->getPharNode($name) !== null;
    }

    /**
     * @param string $name
     *
     * @return \DOMNode
     */
    private function getPharNode($name) {
        return $this->configFile->query(sprintf('//phive:phar[@name="%s"]', mb_strtolower($name)))->item(0);
    }

    /**
     * @return RequestedPhar[]
     */
    public function getPhars() {
        $phars = [];
        /** @var \DOMElement $pharNode */
        foreach ($this->configFile->query('//phive:phar') as $pharNode) {
            if ($pharNode->hasAttribute('url')) {
                $phars[] = RequestedPhar::fromUrl(new Url($pharNode->getAttribute('url')));
            } else {
                $phars[] = RequestedPhar::fromAlias($this->getPharAliasFromNode($pharNode));
            }
        }
        return $phars;
    }

    /**
     * @param \DOMElement $element
     *
     * @return PharAlias
     */
    private function getPharAliasFromNode(\DOMElement $element) {
        $parser = new VersionConstraintParser();
        if ($element->hasAttribute('version')) {
            $version = $parser->parse($element->getAttribute('version'));
        } else {
            $version = new AnyVersionConstraint();
        }
        return new PharAlias($element->getAttribute('name'), $version);
    }

}
