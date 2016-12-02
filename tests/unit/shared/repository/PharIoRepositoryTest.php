<?php
namespace PharIo\Phive;

use PharIo\Version\Version;

/**
 * @covers \PharIo\Phive\PharIoRepository
 */
class PharIoRepositoryTest extends \PHPUnit_Framework_TestCase {

    public function testReturnsExpectedReleases() {
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

        $xmlFile = $this->getXmlFileMock();
        $xmlFile->method('query')->willReturn([$releaseNode1, $releaseNode2]);

        $expectedReleases = new ReleaseCollection();
        $expectedReleases->add(
            new Release(
                'foo',
                new Version('5.3.0'),
                new PharUrl('https://example.com/foo-5.3.0.phar'),
                new Url('https://example.com/foo-5.3.0.phar.asc'),
                new Sha1Hash('aa43f08c9402ca142f607fa2db0b1152cf248d49')
            )
        );
        $expectedReleases->add(
            new Release(
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

    public function testThrowsExceptionIfReleaseHasUnsupportedHashType() {
        $releaseNode = $this->getReleaseNodeMock(
            '5.3.0',
            'https://example.com/foo-5.3.0.phar',
            'foo',
            'bar'
        );

        $requestedPhar = $this->getRequestedPharMock();
        $requestedPhar->method('getAlias')->willReturn($this->getPharAliasMock());

        $xmlFile = $this->getXmlFileMock();
        $xmlFile->method('query')->willReturn([$releaseNode]);

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
     * @return \DOMElement|\PHPUnit_Framework_MockObject_MockObject
     */
    private function getReleaseNodeMock($version, $url, $hashType, $hash) {
        $hashNode = $this->createMock(\DOMElement::class);
        $hashNode->method('getAttribute')->willReturnMap(
            [
                ['type', $hashType],
                ['value', $hash]
            ]
        );

        $signatureNode = $this->createMock(\DOMElement::class);
        $signatureNode->method('getAttribute')->with('url')->willReturn($url . '.asc');

        $hashNodeList = $this->createMock(\DOMNodeList::class);
        $hashNodeList->method('item')->with('0')->willReturn($hashNode);

        $signatureNodeList = $this->createMock(\DOMNodeList::class);
        $signatureNodeList->method('item')->with('0')->willReturn($signatureNode);

        $node = $this->createMock(\DOMElement::class);
        $node->method('getAttribute')->willReturnMap(
            [
                ['version', $version],
                ['url', $url]
            ]
        );
        $node->method('getElementsByTagName')->willReturnMap(
            [
                ['hash', $hashNodeList],
                ['signature', $signatureNodeList]
            ]
        );
        return $node;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|XmlFile
     */
    private function getXmlFileMock() {
        return $this->createMock(XmlFile::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|PharAlias
     */
    private function getPharAliasMock() {
        return $this->createMock(PharAlias::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|RequestedPhar
     */
    private function getRequestedPharMock() {
        return $this->createMock(RequestedPhar::class);
    }

}
