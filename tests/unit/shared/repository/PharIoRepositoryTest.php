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

use DOMDocument;
use DOMElement;
use PharIo\Version\Version;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;

/**
 * @covers \PharIo\Phive\PharIoRepository
 */
class PharIoRepositoryTest extends TestCase {
    /** @var DOMDocument */
    private $domHelper;

    protected function setUp(): void {
        $this->domHelper = new DOMDocument();
    }

    public function testReturnsExpectedReleases(): void {
        $releaseNode1 = $this->getReleaseNodeMock(
            '5.3.0',
            'https://example.com/foo-5.3.0.phar',
            'sha-1',
            'aa43f08c9402ca142f607fa2db0b1152cf248d49'
        );
        $releaseNode2 = $this->getReleaseNodeMock(
            '5.2.12',
            'https://example.com/foo-5.2.12.phar',
            'sha-256',
            '7a8755061d7ac2bc09f25bf6a867031fb945b4b25a6be1fb41b117893065f76c'
        );

        $pharAlias = $this->getPharAliasMock();
        $pharAlias->method('asString')->willReturn('foo');

        $requestedPhar = $this->getRequestedPharMock();
        $requestedPhar->method('getAlias')->willReturn($pharAlias);

        $frag = $this->domHelper->createDocumentFragment();
        $frag->appendChild($releaseNode1);
        $frag->appendChild($releaseNode2);
        $nodeList = $frag->childNodes;

        $xmlFile = $this->getXmlFileMock();
        $xmlFile->method('query')->willReturn($nodeList);

        $expectedReleases = new ReleaseCollection();
        $expectedReleases->add(
            new SupportedRelease(
                'foo',
                new Version('5.3.0'),
                new PharUrl('https://example.com/foo-5.3.0.phar'),
                new Url('https://example.com/foo-5.3.0.phar.asc'),
                new Sha1Hash('aa43f08c9402ca142f607fa2db0b1152cf248d49')
            )
        );
        $expectedReleases->add(
            new SupportedRelease(
                'foo',
                new Version('5.2.12'),
                new PharUrl('https://example.com/foo-5.2.12.phar'),
                new Url('https://example.com/foo-5.2.12.phar.asc'),
                new Sha256Hash('7a8755061d7ac2bc09f25bf6a867031fb945b4b25a6be1fb41b117893065f76c')
            )
        );

        $repository = new PharIoRepository($xmlFile);
        $this->assertEquals($expectedReleases, $repository->getReleasesByRequestedPhar($requestedPhar));
    }

    public function testThrowsExceptionIfReleaseHasUnsupportedHashType(): void {
        $releaseNode = $this->getReleaseNodeMock(
            '5.3.0',
            'https://example.com/foo-5.3.0.phar',
            'foo',
            'bar'
        );

        $requestedPhar = $this->getRequestedPharMock();
        $requestedPhar->method('getAlias')->willReturn($this->getPharAliasMock());

        $frag = $this->domHelper->createDocumentFragment();
        $frag->appendChild($releaseNode);

        $xmlFile = $this->getXmlFileMock();
        $xmlFile->method('query')->willReturn($frag->childNodes);

        $repository = new PharIoRepository($xmlFile);

        $this->expectException(InvalidHashException::class);

        $repository->getReleasesByRequestedPhar($requestedPhar);
    }

    /**
     * @param string $version
     * @param string $url
     * @param string $hashType
     * @param string $hash
     *
     * @return DOMElement|PHPUnit_Framework_MockObject_MockObject
     */
    private function getReleaseNodeMock($version, $url, $hashType, $hash) {
        $hashNode = $this->domHelper->createElement('hash');
        $hashNode->setAttribute('type', $hashType);
        $hashNode->setAttribute('value', $hash);

        $signatureNode = $this->domHelper->createElement('signature');
        $signatureNode->setAttribute('url', $url . '.asc');

        $node = $this->domHelper->createElement('release');
        $node->setAttribute('version', $version);
        $node->setAttribute('url', $url);

        $node->appendChild($hashNode);
        $node->appendChild($signatureNode);

        return $node;
    }

    /**
     * @return PHPUnit_Framework_MockObject_MockObject|XmlFile
     */
    private function getXmlFileMock() {
        return $this->createMock(XmlFile::class);
    }

    /**
     * @return PharAlias|PHPUnit_Framework_MockObject_MockObject
     */
    private function getPharAliasMock() {
        return $this->createMock(PharAlias::class);
    }

    /**
     * @return PHPUnit_Framework_MockObject_MockObject|RequestedPhar
     */
    private function getRequestedPharMock() {
        return $this->createMock(RequestedPhar::class);
    }
}
