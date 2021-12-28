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

use function in_array;
use function sprintf;
use DOMElement;
use DOMNodeList;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;

/**
 * @covers \PharIo\Phive\AuthXmlConfig
 */
class AuthXmlConfigTest extends TestCase {
    public function testGetAuthenticationTypeBasic(): void {
        $xmlFile = $this->getAuthXmlFileMock(
            'example.com',
            ['type', 'credentials'],
            ['credentials' => 'foo', 'type' => 'Basic']
        );

        $authXml = new AuthXmlConfig($xmlFile);

        $auth = $authXml->getAuthentication('example.com');
        $this->assertEquals('Authorization: Basic foo', $auth->asHttpHeaderString());
    }

    public function testGetAuthenticationTypeOther(): void {
        $xmlFile = $this->getAuthXmlFileMock(
            'example.com',
            ['type', 'credentials'],
            ['credentials' => 'foobar', 'type' => 'Bearer']
        );

        $authXml = new AuthXmlConfig($xmlFile);

        $auth = $authXml->getAuthentication('example.com');
        $this->assertEquals('Authorization: Bearer foobar', $auth->asHttpHeaderString());
    }

    public function testGetAuthenticationWithUsernamePassword(): void {
        $xmlFile = $this->getAuthXmlFileMock(
            'example.com',
            ['type', 'username', 'password'],
            ['type' => 'Basic', 'username' => 'Aladdin', 'password' => 'open sesame']
        );

        $authXml = new AuthXmlConfig($xmlFile);

        $auth = $authXml->getAuthentication('example.com');
        $this->assertEquals('Authorization: Basic QWxhZGRpbjpvcGVuIHNlc2FtZQ==', $auth->asHttpHeaderString());
    }

    public function testHasAuthenticationWithNoResult(): void {
        $nodeList = $this->getDomNodeListMock();
        $nodeList->method('count')
            ->willReturn(0);

        $xmlFile = $this->createMock(XmlFile::class);
        $xmlFile
            ->method('query')
            ->with('//phive:domain[@host="example.com"]')
            ->willReturn($nodeList);

        $authXml = new AuthXmlConfig($xmlFile);

        $result = $authXml->hasAuthentication('example.com');
        $this->assertFalse($result);
    }

    public function testGetAuthenticationNoDataException(): void {
        $nodeList = $this->getDomNodeListMock();
        $nodeList->method('count')
            ->willReturn(0);

        $xmlFile = $this->createMock(XmlFile::class);
        $xmlFile
            ->method('query')
            ->with('//phive:domain[@host="example.com"]')
            ->willReturn($nodeList);

        $authXml = new AuthXmlConfig($xmlFile);

        $this->expectException(AuthException::class);
        $this->expectExceptionMessage('No authentication data for example.com');

        $authXml->getAuthentication('example.com');
    }

    public function testGetAuthenticationNoTypeException(): void {
        $xmlFile = $this->getAuthXmlFileMock(
            'example.com',
            [],
            []
        );

        $authXml = new AuthXmlConfig($xmlFile);

        $this->expectException(AuthException::class);
        $this->expectExceptionMessage('Authentication data for example.com is invalid');

        $authXml->getAuthentication('example.com');
    }

    public function testGetAuthenticationNoCredentialsException(): void {
        $xmlFile = $this->getAuthXmlFileMock(
            'example.com',
            ['type'],
            ['type' => 'Token']
        );

        $authXml = new AuthXmlConfig($xmlFile);

        $this->expectException(AuthException::class);
        $this->expectExceptionMessage('Authentication data for example.com is invalid');

        $authXml->getAuthentication('example.com');
    }

    public function testGetAuthenticationMalformattedBasicException(): void {
        $xmlFile = $this->getAuthXmlFileMock(
            'example.com',
            ['type', 'username'],
            ['type' => 'Basic', 'username' => 'Aladdin']
        );

        $authXml = new AuthXmlConfig($xmlFile);

        $this->expectException(AuthException::class);
        $this->expectExceptionMessage('Basic authentication data for example.com is invalid');

        $authXml->getAuthentication('example.com');
    }

    public function testGetAuthenticationEmptyBasicException(): void {
        $xmlFile = $this->getAuthXmlFileMock(
            'example.com',
            ['type', 'username', 'password'],
            ['type' => 'Basic', 'username' => 'Aladdin', 'password' => '']
        );

        $authXml = new AuthXmlConfig($xmlFile);

        $this->expectException(AuthException::class);
        $this->expectExceptionMessage('Basic authentication data for example.com is invalid');

        $authXml->getAuthentication('example.com');
    }

    /**
     * @return PHPUnit_Framework_MockObject_MockObject|XmlFile
     */
    private function getAuthXmlFileMock(string $domain, array $hasAttributeList, array $getAttributeList) {
        $node = $this->getDomElementMock();
        $node
            ->method('getAttribute')
            ->willReturnCallback(static function ($name) use ($getAttributeList) {
                return $getAttributeList[$name] ?? '';
            });
        $node
            ->method('hasAttribute')
            ->willReturnCallback(static function ($name) use ($hasAttributeList) {
                return in_array($name, $hasAttributeList, true);
            });

        $nodeList = $this->getDomNodeListMock();
        $nodeList->method('item')
            ->with(0)
            ->willReturn($node);
        $nodeList->method('count')
            ->willReturn(1);

        $xmlFile = $this->createMock(XmlFile::class);
        $xmlFile
            ->method('query')
            ->with(sprintf('//phive:domain[@host="%s"]', $domain))
            ->willReturn($nodeList);

        return $xmlFile;
    }

    /**
     * @return DOMElement|PHPUnit_Framework_MockObject_MockObject
     */
    private function getDomElementMock() {
        return $this->createMock(DOMElement::class);
    }

    /**
     * @return DOMNodeList|PHPUnit_Framework_MockObject_MockObject
     */
    private function getDomNodeListMock() {
        return $this->createMock(DOMNodeList::class);
    }
}
