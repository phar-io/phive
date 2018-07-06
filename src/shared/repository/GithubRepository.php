<?php
namespace PharIo\Phive;

use PharIo\Version\InvalidVersionException;

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
        $name = $requestedPhar->getAlias()->asString();

        foreach ($this->jsonData->getParsed() as $entry) {
            try {
                $version = new GitHubVersion($entry->tag_name);
            } catch (InvalidVersionException $exception) {
                // we silently ignore invalid version identifiers for now as they are
                // likely to be an arbitrary tag that erroneously got promoted to release
                continue;
            }
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

            // we do seem to have a version but no phar asset - can't use?
            if (!$pharUrl instanceof Url) {
                $releases->add(
                    new UnsupportedRelease($name, $version, 'No downloadable PHAR')
                );
                continue;
            }

            // we do have a phar but no signature - can't use either
            if (!$signatureUrl instanceof Url) {
                $releases->add(
                    new UnsupportedRelease($name, $version, 'No GPG signature')
                );
                continue;
            }

            $releases->add(
                // Github doesn't publish any hashes for the files :-(
                new SupportedRelease(
                    $name,
                    $version,
                    $pharUrl,
                    $signatureUrl
                )
            );
        }

        return $releases;
    }

}
