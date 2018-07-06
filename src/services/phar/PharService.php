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
     * @param PharRegistry   $registry
     * @param PharDownloader $downloader
     */
    public function __construct(PharRegistry $registry, PharDownloader $downloader) {
        $this->registry = $registry;
        $this->downloader = $downloader;
    }

    /**
     * @param SupportedRelease $release
     *
     * @return Phar
     */
    public function getPharFromRelease(SupportedRelease $release) {

        if ($this->registry->hasPhar($release->getName(), $release->getVersion())) {

            return $this->registry->getPhar($release->getName(), $release->getVersion());
        }

        return $this->registry->addPhar(
            $this->downloader->download($release)
        );
    }

}
