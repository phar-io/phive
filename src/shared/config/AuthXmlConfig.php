<?php declare(strict_types=1);
/*
 * This file is part of Phive.
 *
 * Copyright (c) Arne Blankerts <arne@blankerts.de>, Sebastian Heuer <sebastian@phpeople.de> and contributors
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */
namespace PharIo\Phive;

use function sprintf;
use function strpos;
use DOMElement;

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
        $query  = sprintf('//phive:domain[@host="%s"]', $domain);
        $result = $this->xmlFile->query($query);

        return $result->count() > 0;
    }

    /**
     * @throws AuthException
     */
    public function getAuthentication(string $domain): Authentication {
        if (!$this->hasAuthentication($domain)) {
            throw new AuthException(sprintf('No authentication data for %s', $domain));
        }

        $query  = sprintf('//phive:domain[@host="%s"]', $domain);
        $result = $this->xmlFile->query($query);

        /** @var DOMElement $auth */
        $auth = $result->item(0);

        if (!$auth->hasAttribute('type')) {
            throw new AuthException(sprintf('Authentication data for %s is invalid', $domain));
        }

        $authType = strtolower($auth->getAttribute('type'));

        if ($authType === 'basic') {
            return $this->handleBasicAuthentication($domain, $auth);
        }

        if (!$auth->hasAttribute('credentials') || empty($auth->getAttribute('credentials'))) {
            throw new AuthException(sprintf('Authentication data for %s is invalid', $domain));
        }

        $authCredentials = $auth->getAttribute('credentials');

        switch ($authType) {
            case 'token':
                return new TokenAuthentication($authCredentials);
            case 'bearer':
                return new BearerAuthentication($authCredentials);

            default:
                throw new AuthException(sprintf('Invalid authentication type for %s', $domain));
        }
    }

    /**
     * @throws AuthException
     */
    private function handleBasicAuthentication(string $domain, DOMElement $node): Authentication {
        if (
            $node->hasAttribute('username') &&
            !empty($username = $node->getAttribute('username')) &&
            strpos($username, ':') === false &&
            $node->hasAttribute('password') &&
            !empty($node->getAttribute('password'))
        ) {
            return BasicAuthentication::fromLoginPassword($username, $node->getAttribute('password'));
        }

        if ($node->hasAttribute('credentials') && !empty($node->getAttribute('credentials'))) {
            return new BasicAuthentication($node->getAttribute('credentials'));
        }

        throw new AuthException(sprintf('Basic authentication data for %s is invalid', $domain));
    }
}
