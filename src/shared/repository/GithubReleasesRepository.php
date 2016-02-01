<?php
namespace PharIo\Phive;

class GithubReleasesRepository implements ReleasesRepository {

    public function __construct(Json $json) {
        $this->filename = $filename;
    }
    /**
     * @param PharAlias $alias
     */
    public function getReleasesByAlias(PharAlias $alias) {
        $releases = new ReleaseCollection();
        $query = sprintf('//phive:phar[@name="%s"]/phive:release', $alias);
        foreach ($this->getXPath()->query($query) as $releaseNode) {
            /** @var \DOMElement $releaseNode */
            $releases->add(
                new Release(
                    new Version($releaseNode->getAttribute('version')),
                    new Url($releaseNode->getAttribute('url')),
                    $this->getHash($releaseNode)
                )
            );
        }
        return $releases;

    }

}
