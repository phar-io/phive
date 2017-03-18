<?php
namespace PharIo\Phive;

class PharService {

    /**
     * @var PharRegistry
     */
    private $registry;

    /**
     * @var PharDownloader
     */
    private $downloader;

    /**
     * @param PharRegistry $registry
     * @param PharDownloader $downloader
     */
    public function __construct(PharRegistry $registry, PharDownloader $downloader) {
        $this->registry = $registry;
        $this->downloader = $downloader;
    }

    /**
     * @param Release $release
     *
     * @return Phar
     */
    public function getPharFromRelease(Release $release) {

        if ($this->registry->hasPhar($release->getName(), $release->getVersion())) {
            return $this->registry->getPhar($release->getName(), $release->getVersion());
        }
        $phar = $this->downloader->download($release);
        $this->registry->addPhar($phar);

        return $phar;
    }

}
