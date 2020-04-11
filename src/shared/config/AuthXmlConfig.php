<?php declare(strict_types = 1);
namespace PharIo\Phive;

class AuthXmlConfig implements AuthConfig {
    /** @var XmlFile */
    private $xmlFile;

    /**
     * AuthXmlConfig constructor.
     */
    public function __construct(XmlFile $xmlFile) {
        $this->xmlFile = $xmlFile;
    }

    public function hasAuthentication(string $domain): bool {
        $query  = \sprintf('//phive:domain[@host="%s"]', $domain);
        $result = $this->xmlFile->query($query);

        return $result->count() > 0;
    }

    public function getAuthentication(string $domain): Authentication {
        $query  = \sprintf('//phive:domain[@host="%s"]', $domain);
        $result = $this->xmlFile->query($query);

        /** @var \DOMElement $auth */
        $auth = $result->item(0);

        if (\mb_strtolower($auth->getAttribute('type')) === 'basic' && $auth->hasAttribute('username')) {
            return Authentication::fromLoginPassword($domain, $auth->getAttribute('username'), $auth->getAttribute('password'));
        }

        return new Authentication($domain, $auth->getAttribute('type'), $auth->getAttribute('credentials'));
    }
}
