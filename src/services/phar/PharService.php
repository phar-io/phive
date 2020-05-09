<?php declare(strict_types = 1);
namespace PharIo\Phive;

class PharService {
    /** @var PharRegistry */
    private $registry;

    /** @var PharDownloader */
    private $downloader;

    public function __construct(PharRegistry $registry, PharDownloader $downloader) {
        $this->registry   = $registry;
        $this->downloader = $downloader;
    }

    public function getPharFromRelease(SupportedRelease $release): Phar {
        if ($this->registry->hasPhar($release->getName(), $release->getVersion())) {
            return $this->registry->getPhar($release->getName(), $release->getVersion());
        }

        return $this->registry->addPhar(
            $this->downloader->download($release)
        );
    }
}
