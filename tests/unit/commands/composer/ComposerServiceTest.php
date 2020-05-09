<?php declare(strict_types = 1);
namespace PharIo\Phive;

use PharIo\FileSystem\Filename;
use PharIo\Version\AnyVersionConstraint;
use PharIo\Version\ExactVersionConstraint;
use PHPUnit\Framework\TestCase;

/**
 * @covers \PharIo\Phive\ComposerService
 */
class ComposerServiceTest extends TestCase {
    public function testFindCandidatesReturnsExpectedPhars(): void {
        $filename = new Filename(__DIR__ . '/fixtures/composer.json');

        $sourcesList = $this->getSourcesListMock();
        $sourcesList->method('getAliasForComposerAlias')
            ->willReturnCallback(
                function (ComposerAlias $composerAlias) {
                    switch ($composerAlias) {
                        case new ComposerAlias('theseer/autoload'):
                            return 'phpab';
                        case new ComposerAlias('foo/bar'):
                            throw new SourcesListException();
                        case new ComposerAlias('phpunit/phpunit'):
                            return 'phpunit';
                    }
                }
            );

        $expectedList = [
            new RequestedPhar(new PharAlias('phpab'), new ExactVersionConstraint('1.20.1'), new ExactVersionConstraint('1.20.1')),
            new RequestedPhar(new PharAlias('phpunit'), new AnyVersionConstraint(), new AnyVersionConstraint()),
        ];

        $service = new ComposerService($sourcesList);
        $this->assertEquals($expectedList, $service->findCandidates($filename));
    }

    public function testThrowsExceptionIfComposerFileDoesNotExist(): void {
        $filename = new Filename(__DIR__ . '/fixtures/foo.json');

        $service = new ComposerService($this->getSourcesListMock());

        $this->expectException(\InvalidArgumentException::class);
        $service->findCandidates($filename);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|SourcesList
     */
    private function getSourcesListMock() {
        return $this->createMock(SourcesList::class);
    }
}
