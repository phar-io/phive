<?php
namespace PharIo\Phive;

class GithubAliasResolver extends AbstractRequestedPharResolver {

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
     * @param RequestedPhar $requestedPhar
     *
     * @return SourceRepository
     */
    public function resolve(RequestedPhar $requestedPhar) {
        if (!$requestedPhar->hasAlias()) {
            return $this->tryNext($requestedPhar);
        }

        $name = $requestedPhar->getAlias()->asString();
        if (strpos($name, '/') === false) {
            return $this->tryNext($requestedPhar);
        }
        try {
            return $this->localResolve($name);
        } catch (HttpException $e) {
            return $this->tryNext($requestedPhar);
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
