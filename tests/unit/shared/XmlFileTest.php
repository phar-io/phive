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

use org\bovigo\vfs\vfsStream;
use PharIo\FileSystem\Directory;
use PharIo\FileSystem\File;
use PharIo\FileSystem\Filename;
use PHPUnit\Framework\TestCase;

/**
 * @covers \PharIo\Phive\XmlFile
 */
class XmlFileTest extends TestCase {
    public function testCanQueryWithoutContextNode(): void {
        $file = new XmlFile(
            new Filename(__DIR__ . '/fixtures/xmlFile.xml'),
            'https://phar.io/phive/test',
            'root'
        );

        $actual = $file->query('//phive:node1');
        $this->assertSame(1, $actual->length);
        $this->assertSame('Node 1 Value', $actual->item(0)->nodeValue);
    }

    public function testCanQueryWithContextNode(): void {
        $file = new XmlFile(
            new Filename(__DIR__ . '/fixtures/xmlFile.xml'),
            'https://phar.io/phive/test',
            'root'
        );

        $context = $file->getDom()->getElementsByTagName('child')->item(0);

        $actual = $file->query('phive:node1', $context);
        $this->assertSame(1, $actual->length);
        $this->assertSame('Node 1 Value', $actual->item(0)->nodeValue);
    }

    public function testFromFileWithValidXml(): void {
        $path = __DIR__ . '/fixtures/xmlFile.xml';
        $file = XmlFile::fromFile(new File(new Filename($path), file_get_contents($path)));

        $actual = $file->query('//phive:node1');
        $this->assertSame(1, $actual->length);
        $this->assertSame('Node 1 Value', $actual->item(0)->nodeValue);
    }

    public function testFromFileWithEmptyContent(): void {
        $path = __DIR__ . '/fixture/xmlFile.xml';

        $this->expectException(InvalidXmlException::class);
        $file = XmlFile::fromFile(new File(new Filename($path), ''));
    }

    public function testSavesXmlToFile(): void {
        $mockedDirectory = vfsStream::setup('test');

        $this->assertFalse($mockedDirectory->hasChild('someFile.xml'));

        $file = new XmlFile(
            new Filename(vfsStream::url('test/someFile.xml')),
            'https://phar.io/phive/test',
            'root'
        );
        $file->save();

        $this->assertTrue($mockedDirectory->hasChild('someFile.xml'));
    }

    public function testCreatesExpectedDomElement(): void {
        $file = new XmlFile(
            new Filename(__DIR__ . '/fixtures/xmlFile.xml'),
            'https://phar.io/phive/test',
            'root'
        );

        $element = $file->createElement('someElement', 'someValue');

        $this->assertSame('someElement', $element->nodeName);
        $this->assertSame('someValue', $element->nodeValue);
    }

    public function testReturnsExpectedDirectory(): void {
        $file = new XmlFile(
            new Filename(__DIR__ . '/fixtures/xmlFile.xml'),
            'https://phar.io/phive/test',
            'root'
        );

        $expected = new Directory(__DIR__ . '/fixtures');
        $this->assertEquals($expected, $file->getDirectory());
    }

    public function testAddsElementToDom(): void {
        $file = new XmlFile(
            new Filename(__DIR__ . '/fixtures/xmlFile.xml'),
            'https://phar.io/phive/test',
            'root'
        );

        $element = $file->createElement('foo', 'bar');

        $file->addElement($element);

        $this->assertSame(1, $file->getDom()->getElementsByTagName('foo')->length);
        $this->assertSame('bar', $file->getDom()->getElementsByTagName('foo')->item(0)->nodeValue);
    }
}
