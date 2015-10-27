<?php
namespace PharIo\Phive;

class PhiveXmlConfig extends XmlRepository
{
    /**
     * @return array
     */
    public function getPhars()
    {
        $phars = [];
        /** @var \DOMElement $pharNode */
        foreach ($this->getXPath()->query('//phive:phar') as $pharNode) {
            if ($pharNode->hasAttribute('url')) {
                $phars[] = new Url($pharNode->getAttribute('url'));
            } else {
                $phars[] = $this->getPharAliasFromNode($pharNode);
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