<?php
namespace PharIo\Phive;

/**
 * @covers PharIo\Phive\PhiveXmlConfig
 */
class PhiveXmlConfigTest extends \PHPUnit_Framework_TestCase {

    public function testAddPharUpdatesExistingNode() {
        $node = $this->getDomElementMock();
        $node->expects($this->at(0))->method('setAttribute')
            ->with('version', '5.3.0');
        $node->expects($this->at(1))->method('setAttribute')
            ->with('installed', '5.3.0');

        $items = $this->getDomNodeListMock();
        $items->method('item')->with(0)->willReturn($node);

        $configFile = $this->getXmlFileMock();
        $configFile->method('query')
            ->with('//phive:phar[@name="phpunit"]')
            ->willReturn($items);

        $alias = new PharAlias('phpunit', new ExactVersionConstraint('5.3.0'));
        $version = new Version('5.3.0');

        $phar = $this->getRequestedPharMock();
        $phar->method('getAlias')->willReturn($alias);

        $installedPhar = $this->getPharMock();
        $installedPhar->method('getVersion')->willReturn($version);

        $config = new PhiveXmlConfig($configFile);

        $configFile->expects($this->once())->method('save');
        $config->addPhar($phar, $installedPhar);
    }

    public function testAddPharCreatesNewNode() {
        $node = $this->getDomElementMock();
        $node->expects($this->at(0))
            ->method('setAttribute')
            ->with('name', 'phpunit');
        $node->expects($this->at(1))
            ->method('setAttribute')
            ->with('version', '5.3.0');

        $items = $this->getDomNodeListMock();
        $items->method('item')->with(0)->willReturn(null);

        $configFile = $this->getXmlFileMock();
        $configFile->method('query')
            ->with('//phive:phar[@name="phpunit"]')
            ->willReturn($items);
        $configFile->expects($this->once())->method('createElement')->with('phar')
            ->willReturn($node);
        $configFile->expects($this->once())->method('addElement')->with($node);

        $alias = new PharAlias('phpunit', new ExactVersionConstraint('5.3.0'));
        $version = new Version('5.3.0');

        $installedPhar = $this->getPharMock();
        $installedPhar->method('getVersion')->willReturn($version);

        $phar = $this->getRequestedPharMock();
        $phar->method('getAlias')->willReturn($alias);

        $config = new PhiveXmlConfig($configFile);

        $configFile->expects($this->once())->method('save');
        $config->addPhar($phar, $installedPhar);
    }

    public function testGetPharsReturnsExpectedPhars() {
        $node1 = $this->getDomElementMock();
        $node1->method('hasAttribute')->with('url')->willReturn(true);
        $node1->method('getAttribute')->with('url')->willReturn('https://example.com/phpunit-5.3.0.phar');

        $node2 = $this->getDomElementMock();
        $node2->method('hasAttribute')->willReturnMap(
            [
                ['url', false],
                ['version', true]
            ]
        );
        $node2->method('getAttribute')->willReturnMap(
            [
                ['version', '5.2.12'],
                ['name', 'phpunit']
            ]
        );

        $node3 = $this->getDomElementMock();
        $node3->method('hasAttribute')->willReturnMap(
            [
                ['url', false],
                ['version', false]
            ]
        );
        $node3->method('getAttribute')->with('name')->willReturn('phpunit');

        $configFile = $this->getXmlFileMock();
        $configFile->method('query')->with('//phive:phar')
            ->willReturn([$node1, $node2, $node3]);

        $config = new PhiveXmlConfig($configFile);
        $expected = [
            new RequestedPharUrl(new PharUrl('https://example.com/phpunit-5.3.0.phar')),
            new RequestedPharAlias(new PharAlias('phpunit', new ExactVersionConstraint('5.2.12'))),
            new RequestedPharAlias(new PharAlias('phpunit', new AnyVersionConstraint()))
        ];
        $actual = $config->getPhars();

        $this->assertEquals($expected, $actual);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\DOMElement
     */
    private function getDomElementMock() {
        return $this->createMock(\DOMElement::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\DOMNodeList
     */
    private function getDomNodeListMock() {
        return $this->createMock(\DOMNodeList::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|RequestedPhar
     */
    private function getRequestedPharMock() {
        return $this->createMock(RequestedPhar::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Phar
     */
    private function getPharMock() {
        return $this->createMock(Phar::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|XmlFile
     */
    private function getXmlFileMock() {
        return $this->createMock(XmlFile::class);
    }

}
