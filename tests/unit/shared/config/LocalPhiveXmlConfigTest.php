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

use function implode;
use DOMDocument;
use DOMElement;
use DOMNodeList;
use PharIo\FileSystem\Directory;
use PharIo\FileSystem\Filename;
use PharIo\Version\AnyVersionConstraint;
use PharIo\Version\ExactVersionConstraint;
use PharIo\Version\Version;
use PharIo\Version\VersionConstraintParser;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;

/**
 * @covers \PharIo\Phive\LocalPhiveXmlConfig
 * @covers \PharIo\Phive\PhiveXmlConfig
 */
class LocalPhiveXmlConfigTest extends TestCase {
    /** @var DOMDocument */
    private $domHelper;

    protected function setUp(): void {
        $this->domHelper = new DOMDocument();
    }

    public function testAddPharUpdatesExistingNode(): void {
        $node = $this->getDomElementMock();
        $node->method('getAttribute')
            ->with('name')
            ->willReturn('phpunit');

        /*
        $node->expects($this->once())->method('setAttribute')
            ->with('version', '5.3.0');
        $node->expects($this->once())->method('setAttribute')
            ->with('installed', '5.3.0');
        */
        $items = $this->getDomNodeListMock();
        $items->method('item')->with(0)->willReturn($node);

        $nodeListMock = $this->createMock(DOMNodeList::class);
        $nodeListMock->method('item')->with(0)->willReturn($node);

        $configFile = $this->getXmlFileMock();
        $configFile->method('query')
            ->with('//phive:phar')
            ->willReturn($nodeListMock);

        $alias = new PharAlias('phpunit');

        $phar = $this->getRequestedPharMock();
        $phar->method('getAlias')->willReturn($alias);

        $filename = $this->getFilenameMock();
        $filename->method('getRelativePathTo')->willReturn($filename);

        $installedPhar = $this->getInstalledPharMock();
        $installedPhar->method('getVersionConstraint')->willReturn(new ExactVersionConstraint('5.3.0'));
        $installedPhar->method('getInstalledVersion')->willReturn(new Version('5.3.0'));
        $installedPhar->method('getName')->willReturn('phpunit');
        $installedPhar->method('getLocation')->willReturn($filename);

        $config = new LocalPhiveXmlConfig($configFile, $this->getVersionConstraintParserMock(), $this->getEnvironmentMock());

        $configFile->expects($this->once())->method('save');

        $config->addPhar($installedPhar, $phar);
    }

    public function testFindsPharNodesWithoutMatchingCase(): void {
        $xmlFile = new XmlFile(new Filename(__DIR__ . '/fixtures/phive.xml'), 'https://phar.io/phive', 'phive');
        $config  = new LocalPhiveXmlConfig($xmlFile, new VersionConstraintParser(), $this->getEnvironmentMock());
        $this->assertTrue($config->hasPhar('theseer/AUTOLOAD'));
    }

    public function testAddPharCreatesNewNode(): void {
        $node = $this->getDomElementMock();
        //$node->expects($this->at(0))
        //    ->method('setAttribute')
        //    ->with('version', '5.3.0');
        //$node->expects($this->at(2))
        //    ->method('setAttribute')
        //    ->with('name', 'phpunit');

        $nodeListMock = $this->createMock(DOMNodeList::class);
        $nodeListMock->method('item')->with(0)->willReturn($node);

        $configFile = $this->getXmlFileMock();
        $configFile->method('query')
            ->with('//phive:phar')
            ->willReturn($nodeListMock);
        $configFile->expects($this->once())->method('createElement')->with('phar')
            ->willReturn($node);
        $configFile->expects($this->once())->method('addElement')->with($node);

        $alias = new PharAlias('phpunit');

        $filename = $this->getFilenameMock();
        $filename->method('getRelativePathTo')->willReturn($filename);

        $installedPhar = $this->getInstalledPharMock();
        $installedPhar->method('getVersionConstraint')->willReturn(new ExactVersionConstraint('5.3.0'));
        $installedPhar->method('getInstalledVersion')->willReturn(new Version('5.3.0'));
        $installedPhar->method('getName')->willReturn('phpunit');
        $installedPhar->method('getLocation')->willReturn($filename);

        $targetDirectory = $this->getDirectoryMock();
        $targetDirectory->method('getRelativePathTo')->willReturn($this->getDirectoryMock()->asString());

        $phar = $this->getRequestedPharMock();
        $phar->method('getAlias')->willReturn($alias);

        $config = new LocalPhiveXmlConfig($configFile, $this->getVersionConstraintParserMock(), $this->getEnvironmentMock());

        $configFile->expects($this->once())->method('save');
        $configFile->method('getDirectory')->willReturn($this->getDirectoryMock());

        $config->addPhar($installedPhar, $phar);
    }

    public function testGetPharsReturnsExpectedPhars(): void {
        $node1 = $this->domHelper->createElement('node');
        $node1->setAttribute('url', 'https://example.com/phpunit-5.3.0.phar');

        $locationNode2 = implode(DIRECTORY_SEPARATOR, [__DIR__, 'fixtures', 'tools', 'phpunit']);
        $node2         = $this->domHelper->createElement('node');
        $node2->setAttribute('version', '5.2.12');
        $node2->setAttribute('name', 'phpunit');
        $node2->setAttribute('installed', '5.2.12');
        $node2->setAttribute('location', $locationNode2);

        $node3 = $this->domHelper->createElement('node');
        $node3->setAttribute('name', 'phpunit');
        $node3->setAttribute('version', '5.2.12');

        $frag = $this->domHelper->createDocumentFragment();
        $frag->appendChild($node1);
        $frag->appendChild($node2);
        $frag->appendChild($node3);

        $parserMock = $this->getVersionConstraintParserMock();
        $parserMock->method('parse')->willReturn(new AnyVersionConstraint());

        $configFile = $this->getXmlFileMock();
        $configFile->method('query')->with('//phive:phar')
            ->willReturn($frag->childNodes);

        $config   = new LocalPhiveXmlConfig($configFile, $parserMock, $this->getEnvironmentMock());
        $expected = [
            new ConfiguredPhar('https://example.com/phpunit-5.3.0.phar', new AnyVersionConstraint(), null, null, new PharUrl('https://example.com/phpunit-5.3.0.phar')),
            new ConfiguredPhar('phpunit', new AnyVersionConstraint(), new Version('5.2.12'), new Filename($locationNode2)),
            new ConfiguredPhar('phpunit', new AnyVersionConstraint()),
        ];
        $actual = $config->getPhars();

        $this->assertEquals($expected, $actual);
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

    /**
     * @return PHPUnit_Framework_MockObject_MockObject|RequestedPhar
     */
    private function getRequestedPharMock() {
        return $this->createMock(RequestedPhar::class);
    }

    /**
     * @return PHPUnit_Framework_MockObject_MockObject|XmlFile
     */
    private function getXmlFileMock() {
        return $this->createMock(XmlFile::class);
    }

    /**
     * @return Directory|PHPUnit_Framework_MockObject_MockObject
     */
    private function getDirectoryMock() {
        return $this->createMock(Directory::class);
    }

    /**
     * @return PHPUnit_Framework_MockObject_MockObject|VersionConstraintParser
     */
    private function getVersionConstraintParserMock() {
        return $this->createMock(VersionConstraintParser::class);
    }

    /**
     * @return InstalledPhar|PHPUnit_Framework_MockObject_MockObject
     */
    private function getInstalledPharMock() {
        return $this->createMock(InstalledPhar::class);
    }

    /**
     * @return Filename|PHPUnit_Framework_MockObject_MockObject
     */
    private function getFilenameMock() {
        return $this->createMock(Filename::class);
    }

    /**
     * @return Environment|PHPUnit_Framework_MockObject_MockObject
     */
    private function getEnvironmentMock() {
        return $this->createMock(Environment::class);
    }
}
