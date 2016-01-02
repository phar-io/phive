<?php
namespace PharIo\Phive;

class PhiveXmlConfig extends WritableXmlRepository
{

    /**
     * @param RequestedPhar $requestedPhar
     */
    public function addPhar(RequestedPhar $requestedPhar)
    {
        $name = (string)$requestedPhar->getAlias();
        if ($this->hasPharNode($name)) {
            $pharNode = $this->getPharNode($name);
        } else {
            $pharNode = $this->getDom()->createElementNS($this->getNamespace(), 'phar');
            $pharNode->setAttribute('name', $name);
            $this->getDom()->firstChild->appendChild($pharNode);
        }
        $pharNode->setAttribute('version', $requestedPhar->getAlias()->getVersionConstraint()->asString());
        $this->save();
    }

    /**
     * @return RequestedPhar[]
     */
    public function getPhars()
    {
        $phars = [];
        /** @var \DOMElement $pharNode */
        foreach ($this->getXPath()->query('//phive:phar') as $pharNode) {
            if ($pharNode->hasAttribute('url')) {
                $phars[] = RequestedPhar::fromUrl(new Url($pharNode->getAttribute('url')));
            } else {
                $phars[] = RequestedPhar::fromAlias($this->getPharAliasFromNode($pharNode));
            }
        }
        return $phars;
    }

    /**
     * @param string $name
     *
     * @return \DOMNode
     */
    private function getPharNode($name)
    {
        return $this->getXPath()->query(sprintf('//phive:phar[@name="%s"]', mb_strtolower($name)))->item(0);
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    private function hasPharNode($name)
    {
        return $this->getPharNode($name) !== null;
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

    /**
     * @return string
     */
    protected function getRootElementName() {
        return 'phive';
    }

    /**
     * @return string
     */
    protected function getNamespace()
    {
        return 'https://phar.io/phive';
    }

}