<?php
namespace PharIo\Phive;

class DirectUrlResolver extends AbstractRequestedPharResolver {

    /**
     * @param RequestedPhar $requestedPhar
     *
     * @return UrlRepository
     */
    public function resolve(RequestedPhar $requestedPhar) {
        if (!$requestedPhar->hasUrl()) {
            return $this->tryNext($requestedPhar);
        }
        return new UrlRepository();
    }
}
