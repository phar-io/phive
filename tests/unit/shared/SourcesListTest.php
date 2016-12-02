<?php
namespace PharIo\Phive;

use PharIo\Version\AnyVersionConstraint;

/**
 * @covers PharIo\Phive\SourcesList
 */
class SourcesListTest extends \PHPUnit_Framework_TestCase {

    public function testThrowsExceptionForUnknownAlias() {
        $this->expectException(SourcesListException::class);
        $this->getSourcesList()->getSourceForAlias(
            new PharAlias('foo', new AnyVersionConstraint(), new AnyVersionConstraint()
            )
        );
    }

    public function testThrowsExceptionIfMultipleRepositoriesAreMatching() {
        $this->expectException(SourcesListException::class);
        $this->getSourcesList()->getSourceForAlias(
            new PharAlias('phpunit', new AnyVersionConstraint(), new AnyVersionConstraint())
        );
    }

    public function testReturnsExpectedSource() {
        $expected = new Source('phar.io', new Url('https://phar.io'));
        $this->assertEquals(
            $expected,
            $this->getSourcesList()->getSourceForAlias(
                new PharAlias('phpab', new AnyVersionConstraint(), new AnyVersionConstraint())
            )
        );
    }

    /**
     * @return SourcesList
     */
    private function getSourcesList() {
        return new SourcesList(
            new XmlFile(
                new Filename(__DIR__ . '/../../data/repositories.xml'),
                'https://phar.io/repository-list',
                'repositories'
            )
        );
    }

}



