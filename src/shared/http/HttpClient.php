<?php
namespace PharIo\Phive;

interface HttpClient {

    /**
     * @param Url                 $url
     * @param array               $params
     * @param HttpProgressHandler $handler
     *
     * @return HttpResponse
     *
     * @throws HttpException
     */
    public function get(Url $url, array $params = [], HttpProgressHandler $handler = null);

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
