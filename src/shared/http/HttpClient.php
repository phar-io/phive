<?php
namespace PharIo\Phive;

interface HttpClient {

    /**
     * @param Url       $url
     * @param ETag|null $etag
     *
     * @return HttpResponse
     */
    public function get(Url $url, ETag $etag = null);

    /**
     * @param Url       $url
     * @param ETag|null $etag
     *
     * @return HttpResponse
     */
    public function head(Url $url, ETag $etag = null);

    /**
     * @param HostEntry $entry
     *
     * @return void
     */
    public function setHostEntry(HostEntry $entry);

}
