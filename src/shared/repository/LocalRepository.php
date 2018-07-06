<?php
namespace PharIo\Phive;

use PharIo\FileSystem\Filename;

class LocalRepository implements SourceRepository {

    /**
     * @var PharRegistry
     */
    private $registry;

    /**
     * @param PharRegistry $registry
     */
    public function __construct(PharRegistry $registry) {
        $this->registry = $registry;
    }

    /**
     * @param RequestedPhar $requestedPhar
     *
     * @return ReleaseCollection
     */
    public function getReleasesByRequestedPhar(RequestedPhar $requestedPhar) {
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

    /**
     * @param Filename $filename
     *
     * @return string
     */
    private function getPharName(Filename $filename) {
        $filename = pathinfo($filename->asString(), PATHINFO_BASENAME);

        return $filename;
    }
}
