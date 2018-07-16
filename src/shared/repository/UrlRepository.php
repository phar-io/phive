<?php
namespace PharIo\Phive;

class UrlRepository implements SourceRepository {

    /** @var Url */
    private $url;

    /** @var Url|null */
    private $sigUrl;

    /**
     * UrlRepository constructor.
     *
     * @param Url $url
     * @param Url $sigUrl
     */
    public function __construct(Url $url = null, Url $sigUrl = null) {
        $this->url = $url;
        $this->sigUrl = $sigUrl;
    }

    /**
     * @param RequestedPhar $requestedPhar
     *
     * @return ReleaseCollection
     */
    public function getReleasesByRequestedPhar(RequestedPhar $requestedPhar) {
        $releases = new ReleaseCollection();
        if ($this->url === Null) {
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
