<?php declare(strict_types = 1);
namespace PharIo\Phive;

interface HttpClient {
    public function get(Url $url, ETag $etag = null): HttpResponse;

    public function head(Url $url, ETag $etag = null): HttpResponse;
}
