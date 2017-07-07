<?php
namespace PharIo\Phive;

class UrlRepository implements SourceRepository {

    /**
     * @param RequestedPhar $requestedPhar
     *
     * @return ReleaseCollection
     */
    public function getReleasesByRequestedPhar(RequestedPhar $requestedPhar) {
        $releases = new ReleaseCollection();
        $releases->add(
            new Release(
                $requestedPhar->getUrl()->getPharName(),
                $requestedPhar->getUrl()->getPharVersion(),
                $requestedPhar->getUrl(),
                new Url($requestedPhar->getUrl()->asString() . '.asc')
            )
        );

        return $releases;
    }

}
