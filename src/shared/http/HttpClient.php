<?php
namespace PharIo\Phive;

interface HttpClient {

    /**
     * @param Url $url
     * @param ETag|null $etag
     *
     * @return HttpResponse
     */
    public function get(Url $url, ETag $etag = null);
}
