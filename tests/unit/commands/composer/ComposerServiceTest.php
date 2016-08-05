<?php
namespace PharIo\Phive;

use Prophecy\Prophecy\ObjectProphecy;

/**
 * @covers PharIo\Phive\ComposerService
 */
class ComposerServiceTest extends \PHPUnit_Framework_TestCase {

    public function testFindCandidatesReturnsExpectedPhars() {
        $filename = new Filename(__DIR__ . '/fixtures/composer.json');

        $sourcesList = $this->getSourcesListProphecy();
        $sourcesList->getAliasForComposerAlias(new ComposerAlias('phpunit/phpunit'))
            ->willReturn('phpunit');

        $sourcesList->getAliasForComposerAlias(new ComposerAlias('foo/bar'))
            ->willThrow(new SourcesListException());

        $sourcesList->getAliasForComposerAlias(new ComposerAlias('theseer/autoload'))
            ->willReturn('phpab');

        $expectedList = [
            new RequestedPharAlias(new PharAlias('phpab', new ExactVersionConstraint('1.20.1'), new ExactVersionConstraint('1.20.1'))),
            new RequestedPharAlias(new PharAlias('phpunit', new AnyVersionConstraint(), new AnyVersionConstraint())),
        ];

        $service = new ComposerService($sourcesList->reveal());
        $this->assertEquals($expectedList, $service->findCandidates($filename));
    }

    public function testThrowsExceptionIfComposerFileDoesNotExist() {
        $filename = new Filename(__DIR__ . '/fixtures/foo.json');

        $service = new ComposerService($this->getSourcesListProphecy()->reveal());

        $this->expectException(\InvalidArgumentException::class);
        $service->findCandidates($filename);
    }

    /**
     * @return ObjectProphecy|SourcesList
     */
    private function getSourcesListProphecy() {
        return $this->prophesize(SourcesList::class);
    }

}
