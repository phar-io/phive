<?php
namespace PharIo\Phive;

interface HttpClient {

    /**
     * @param Url $url
     * @param array $params
     *
     * @return HttpResponse
     * @internal param HttpProgressHandler $handler
     *
     */
    public function get(Url $url, array $params = []);

    /**
     * @param Url   $url
     * @param array $params
     *
     * @return HttpResponse
     *
     * @throws HttpException
     */
    public function head(Url $url, array $params = []);
}
