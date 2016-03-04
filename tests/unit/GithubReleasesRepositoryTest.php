<?php
namespace PharIo\Phive;

class GithubReleasesRepositoryTest extends \PHPUnit_Framework_TestCase {

    public function testRepositoryDataCanBeRetrieved() {
        $json = new JsonData(
            file_get_contents(__DIR__ . '/../data/github/releases.json')
        );

        $alias = new PharAlias('phive', new AnyVersionConstraint());

        $repo = new GithubReleasesRepository($json);
        $result = $repo->getReleasesByAlias($alias);

        $this->assertInstanceOf(ReleaseCollection::class, $result);

        $this->assertInstanceOf(Release::class, $result->getLatest(new AnyVersionConstraint()));

    }
}
