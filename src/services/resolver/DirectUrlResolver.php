<?php
namespace PharIo\Phive;

class DirectUrlResolver extends AbstractRequestedPharResolver {

    /**
     * @param RequestedPhar $requestedPhar
     *
     * @return SourceRepository
     */
    public function resolve(RequestedPhar $requestedPhar) {
        if (!$requestedPhar->hasUrl()) {
            return $this->tryNext($requestedPhar);
        }
        return new UrlRepository();
    }
}
