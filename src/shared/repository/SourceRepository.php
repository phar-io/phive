<?php
namespace PharIo\Phive;

interface SourceRepository {

    /**
     * @param RequestedPhar $requestedPhar
     *
     * @return ReleaseCollection
     */
    public function getReleasesByRequestedPhar(RequestedPhar $requestedPhar);
}
