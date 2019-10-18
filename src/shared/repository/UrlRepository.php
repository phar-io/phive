<?php declare(strict_types = 1);
namespace PharIo\Phive;

class UrlRepository implements SourceRepository {

    /** @var null|Url */
    private $url;

    /** @var null|Url */
    private $sigUrl;

    /**
     * UrlRepository constructor.
     */
    public function __construct(Url $url = null, Url $sigUrl = null) {
        $this->url    = $url;
        $this->sigUrl = $sigUrl;
    }

    public function getReleasesByRequestedPhar(RequestedPhar $requestedPhar): ReleaseCollection {
        $releases = new ReleaseCollection();

        if ($this->url === null) {
            return $releases;
        }

        if ($requestedPhar->getUrl()->equals($this->url)) {
            $releases->add(
                new SupportedRelease(
                    $requestedPhar->getUrl()->getPharName(),
                    $requestedPhar->getUrl()->getPharVersion(),
                    $requestedPhar->getUrl(),
                    $this->sigUrl
                )
            );
        }

        return $releases;
    }
}
