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

use function in_array;
use function substr;
use PharIo\Version\InvalidVersionException;
use PharIo\Version\Version;

class GitlabRepository implements SourceRepository {
    /** @var JsonData */
    private $jsonData;

    public function __construct(JsonData $json) {
        $this->jsonData = $json;
    }

    public function getReleasesByRequestedPhar(RequestedPhar $requestedPhar): ReleaseCollection {
        $releases = new ReleaseCollection();
        $name     = $requestedPhar->getAlias()->asString();

        foreach ($this->jsonData->getParsed() as $entry) {
            try {
                $version = new Version($entry['tag_name']);
            } catch (InvalidVersionException $exception) {
                // we silently ignore invalid version identifiers for now as they are
                // likely to be an arbitrary tag that erroneously got promoted to release
                continue;
            }
            $pharUrl      = null;
            $signatureUrl = null;

            foreach ($entry['assets']['links'] as $asset) {
                $assetName = $asset['name'];
                $url       = $asset['url'];

                if (substr($assetName, -5, 5) === '.phar') {
                    $pharUrl = new PharUrl($url);

                    continue;
                }

                if (in_array(substr($assetName, -4, 4), ['.asc', '.sig'], true)) {
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

            // we do have a phar but no signature, could potentially be used
            if (!$signatureUrl instanceof Url) {
                $releases->add(
                    new SupportedRelease($name, $version, $pharUrl)
                );

                continue;
            }

            $releases->add(
                // Gitlab doesn't publish any hashes for the files :-(
                new SupportedRelease($name, $version, $pharUrl, $signatureUrl)
            );
        }

        return $releases;
    }
}
