<?php
namespace PharIo\Phive;

interface HttpClient {

    /**
     * @param Url   $url
     * @param array $params
     *
     * @return HttpResponse
     */
    public function get(Url $url, array $params = []);
}
