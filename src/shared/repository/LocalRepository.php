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

use const PATHINFO_BASENAME;
use function pathinfo;
use PharIo\FileSystem\Filename;

class LocalRepository implements SourceRepository {
    /** @var PharRegistry */
    private $registry;

    public function __construct(PharRegistry $registry) {
        $this->registry = $registry;
    }

    public function getReleasesByRequestedPhar(RequestedPhar $requestedPhar): ReleaseCollection {
        $releases = new ReleaseCollection();

        foreach ($this->registry->getPhars($requestedPhar->asString()) as $phar) {
            if (!$requestedPhar->getLockedVersion()->complies($phar->getVersion())) {
                continue;
            }
            $releases->add(
                new SupportedRelease(
                    $phar->getName(),
                    $phar->getVersion(),
                    new PharUrl('https://example.com/' . $this->getPharName($phar->getFile()->getFilename())),
                    new PharUrl('https://example.com')
                )
            );
        }

        return $releases;
    }

    private function getPharName(Filename $filename): string {
        return pathinfo($filename->asString(), PATHINFO_BASENAME);
    }
}
