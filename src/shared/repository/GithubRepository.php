<?php
namespace PharIo\Phive;

class GithubRepository implements SourceRepository {

    /**
     * @var JsonData
     */
    private $jsonData;

    /**
     * @param JsonData $json
     */
    public function __construct(JsonData $json) {
        $this->jsonData = $json;
    }

    /**
     * @param PharAlias $alias
     *
     * @return ReleaseCollection
     */
    public function getReleasesByAlias(PharAlias $alias) {
        $releases = new ReleaseCollection();

        foreach ($this->jsonData->getParsed() as $entry) {
            $version = new Version($entry->tag_name);

            $pharUrl = null;
            foreach ($entry->assets as $asset) {
                $url = $asset->browser_download_url;
                if (substr($url, -5, 5) === '.phar') {
                    $pharUrl = new Url($url);
                    break;
                }
            }

            // we do seem to have a version but no phar asset?
            if (!$pharUrl instanceof Url) {
                continue;
            }

            $releases->add(
                // Github doesn't publish any hashes for the files :-(
                new Release((string)$alias, $version, $pharUrl)
            );

        }

        return $releases;
    }

}
