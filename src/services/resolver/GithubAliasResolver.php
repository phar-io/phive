<?php
namespace PharIo\Phive;

class GithubAliasResolver extends AbstractAliasResolver {

    /**
     * @var HttpClient
     */
    private $client;

    /**
     * GithubAliasResolver constructor.
     *
     * @param HttpClient $client
     */
    public function __construct(HttpClient $client) {
        $this->client = $client;
    }

    public function resolve(PharAlias $alias) {
        $name = (string)$alias;
        if (strpos($name, '/') !== false) {
            return $this->localResolve($name);
        }
        return $this->tryNext($alias);
    }

    private function localResolve($name) {
        list($username, $project) = explode('/', $name);
        $url = new Url(
            sprintf('https://api.github.com/repos/%s/%s/releases', $username, $project)
        );
        $response = $this->client->head($url);
        if ($response->getHttpCode() === 200) {
            return [new Source('github', $url)];
        }
    }

}
