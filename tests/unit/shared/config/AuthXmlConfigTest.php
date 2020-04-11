<?php declare(strict_types = 1);
namespace PharIo\Phive;

use PHPUnit\Framework\TestCase;

/**
 * @covers \PharIo\Phive\AuthXmlConfig
 */
class AuthXmlConfigTest extends TestCase {
    public function testGetAuthentication(): void {
        $node = $this->getDomElementMock();
        $node
            ->method('getAttribute')
            ->willReturnCallback(function ($name) {
                switch ($name) {
                    case 'type':
                        return 'basic';
                    case 'credentials':
                        return 'foo';
                    default:
                        return;
                }
            });
        $node
            ->method('hasAttribute')
            ->with('username')
            ->willReturn(false);

        $nodeList = $this->getDomNodeListMock();
        $nodeList->method('item')
            ->with(0)
            ->willReturn($node);

        $xmlFile = $this->createMock(XmlFile::class);
        $xmlFile
            ->method('query')
            ->with('//phive:domain[@host="example.com"]')
            ->willReturn($nodeList);

        $authXml = new AuthXmlConfig($xmlFile);

        $auth = $authXml->getAuthentication('example.com');
        $this->assertEquals('Authorization: basic foo', $auth->asString());
    }

    public function testGetAuthenticationWithUsernamePassword(): void {
        $node = $this->getDomElementMock();
        $node
            ->method('getAttribute')
            ->willReturnCallback(function ($name) {
                switch ($name) {
                    case 'type':
                        return 'basic';
                    case 'username':
                        return 'Aladdin';
                    case 'password':
                        return 'open sesame';
                    default:
                        return;
                }
            });
        $node
            ->method('hasAttribute')
            ->with('username')
            ->willReturn(true);

        $nodeList = $this->getDomNodeListMock();
        $nodeList->method('item')
            ->with(0)
            ->willReturn($node);

        $xmlFile = $this->createMock(XmlFile::class);
        $xmlFile
            ->method('query')
            ->with('//phive:domain[@host="example.com"]')
            ->willReturn($nodeList);

        $authXml = new AuthXmlConfig($xmlFile);

        $auth = $authXml->getAuthentication('example.com');
        $this->assertEquals('Authorization: Basic QWxhZGRpbjpvcGVuIHNlc2FtZQ==', $auth->asString());
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

    /**
     * @return \DOMElement|\PHPUnit_Framework_MockObject_MockObject
     */
    private function getDomElementMock() {
        return $this->createMock(\DOMElement::class);
    }

    /**
     * @return \DOMNodeList|\PHPUnit_Framework_MockObject_MockObject
     */
    private function getDomNodeListMock() {
        return $this->createMock(\DOMNodeList::class);
    }
}
