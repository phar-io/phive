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

        $phar = $this->getRequestedPharMock();
        $phar->method('getAlias')->willReturn($alias);

        $installedPhar = $this->getInstalledPharMock();
        $installedPhar->method('getVersionConstraint')->willReturn(new ExactVersionConstraint('5.3.0'));
        $installedPhar->method('getInstalledVersion')->willReturn(new Version('5.3.0'));
        $installedPhar->method('getName')->willReturn('phpunit');
        $installedPhar->method('getLocation')->willReturn($this->getFilenameMock());

        $targetDirectory = $this->getDirectoryMock();
        $targetDirectory->method('getRelativePathTo')->willReturn($this->getDirectoryMock());

        $config = new PhiveXmlConfig($configFile, $this->getVersionConstraintParserMock());

        $configFile->expects($this->once())->method('save');
        $configFile->method('getDirectory')->willReturn($this->getDirectoryMock());

        $config->addPhar($installedPhar);
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


        $installedPhar = $this->getInstalledPharMock();
        $installedPhar->method('getVersionConstraint')->willReturn(new ExactVersionConstraint('5.3.0'));
        $installedPhar->method('getInstalledVersion')->willReturn(new Version('5.3.0'));
        $installedPhar->method('getName')->willReturn('phpunit');
        $installedPhar->method('getLocation')->willReturn($this->getDirectoryMock());

        $targetDirectory = $this->getDirectoryMock();
        $targetDirectory->method('getRelativePathTo')->willReturn($this->getDirectoryMock());

        $phar = $this->getRequestedPharMock();
        $phar->method('getAlias')->willReturn($alias);

        $config = new PhiveXmlConfig($configFile, $this->getVersionConstraintParserMock());

        $configFile->expects($this->once())->method('save');
        $configFile->method('getDirectory')->willReturn($this->getDirectoryMock());

        $config->addPhar($installedPhar);
    }

    public function testGetPharsReturnsExpectedPhars() {
        $node1 = $this->getDomElementMock();
        $node1->method('hasAttribute')->willReturnMap(
            [
                ['url', true],
                ['installed', false]
            ]
        );
        $node1->method('getAttribute')->with('url')->willReturn('https://example.com/phpunit-5.3.0.phar');

        $node2 = $this->getDomElementMock();
        $node2->method('hasAttribute')->willReturnMap(
            [
                ['url', false],
                ['version', true],
                ['installed', true]
            ]
        );
        $node2->method('getAttribute')->willReturnMap(
            [
                ['version', '5.2.12'],
                ['name', 'phpunit'],
                ['installed', '5.2.12']
            ]
        );

        $node3 = $this->getDomElementMock();
        $node3->method('hasAttribute')->willReturnMap(
            [
                ['url', false],
                ['version', true]
            ]
        );
        $node3->method('getAttribute')->willReturnMap(
            [
                ['name', 'phpunit'],
                ['version', '5.2.12']
            ]
        );

        $parserMock = $this->getVersionConstraintParserMock();
        $parserMock->method('parse')->willReturn(new AnyVersionConstraint());

        $configFile = $this->getXmlFileMock();
        $configFile->method('query')->with('//phive:phar')
            ->willReturn([$node1, $node2, $node3]);

        $config = new PhiveXmlConfig($configFile, $parserMock);
        $expected = [
            new ConfiguredPhar('https://example.com/phpunit-5.3.0.phar', new AnyVersionConstraint()),
            new ConfiguredPhar('phpunit', new AnyVersionConstraint(), new Version('5.2.12')),
            new ConfiguredPhar('phpunit', new AnyVersionConstraint()),
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
     * @return \PHPUnit_Framework_MockObject_MockObject|XmlFile
     */
    private function getXmlFileMock() {
        return $this->createMock(XmlFile::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Directory
     */
    private function getDirectoryMock() {
        return $this->createMock(Directory::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|VersionConstraintParser
     */
    private function getVersionConstraintParserMock() {
        return $this->createMock(VersionConstraintParser::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|InstalledPhar
     */
    private function getInstalledPharMock() {
        return $this->createMock(InstalledPhar::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Filename
     */
    private function getFilenameMock() {
        return $this->createMock(Filename::class);
    }

}
