<?php
namespace PharIo\Phive;

use PharIo\Version\Version;

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
     * @param RequestedPhar $requestedPhar
     *
     * @return ReleaseCollection
     */
    public function getReleasesByRequestedPhar(RequestedPhar $requestedPhar) {
        $releases = new ReleaseCollection();

        foreach ($this->jsonData->getParsed() as $entry) {
            $version = new Version($entry->tag_name);

            $pharUrl = null;
            $signatureUrl = null;
            foreach ($entry->assets as $asset) {
                $url = $asset->browser_download_url;
                if (substr($url, -5, 5) === '.phar') {
                    $pharUrl = new PharUrl($url);
                    continue;
                }
                if (in_array(substr($url, -4, 4), ['.asc', '.sig'], true)) {
                    $signatureUrl = new Url($url);
                }
            }

            // we do seem to have a version but no phar asset?
            if (!$pharUrl instanceof Url) {
                continue;
            }

            // we do have a phar but no signature - can't use either
            if (!$signatureUrl instanceof Url) {
                continue;
            }

            $releases->add(
                // Github doesn't publish any hashes for the files :-(
                new Release(
                    $requestedPhar->getAlias()->asString(),
                    $version,
                    $pharUrl,
                    $signatureUrl
                )
            );
        }

        return $releases;
    }

}
