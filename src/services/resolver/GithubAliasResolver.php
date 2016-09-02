<?php
namespace PharIo\Phive;

class GithubAliasResolver extends AbstractAliasResolver {

    /**
     * @var FileDownloader
     */
    private $fileDownloader;

    /**
     * @param FileDownloader $fileDownloader
     */
    public function __construct(FileDownloader $fileDownloader) {
        $this->fileDownloader = $fileDownloader;
    }

    /**
     * @param PharAlias $alias
     *
     * @return GithubRepository
     */
    public function resolve(PharAlias $alias) {
        $name = (string)$alias;
        if (strpos($name, '/') === false) {
            return $this->tryNext($alias);
        }
        try {
            return $this->localResolve($name);
        } catch (HttpException $e) {
            return $this->tryNext($alias);
        }
    }

    /**
     * @param string $name
     *
     * @return GithubRepository
     */
    private function localResolve($name) {
        list($username, $project) = explode('/', $name);
        $url = new Url(
            sprintf('https://api.github.com/repos/%s/%s/releases', $username, $project)
        );

        $file = $this->fileDownloader->download($url);
        return new GithubRepository(
            new JsonData($file->getContent())
        );
    }

}
