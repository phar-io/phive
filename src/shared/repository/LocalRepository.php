<?php declare(strict_types = 1);
namespace PharIo\Phive;

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
        return \pathinfo($filename->asString(), \PATHINFO_BASENAME);
    }
}
