<?php declare(strict_types = 1);
namespace PharIo\Phive;

class GitlabAliasResolver extends AbstractRequestedPharResolver {
    /** @var FileDownloader */
    private $fileDownloader;

    public function __construct(FileDownloader $fileDownloader) {
        $this->fileDownloader = $fileDownloader;
    }

    public function resolve(RequestedPhar $requestedPhar): SourceRepository {
        if (!$requestedPhar->hasAlias()) {
            return $this->tryNext($requestedPhar);
        }

        $name = $requestedPhar->getAlias()->asString();

        if (\strpos($name, '/') === false) {
            return $this->tryNext($requestedPhar);
        }

        try {
            return $this->localResolve($name);
        } catch (DownloadFailedException $e) {
            return $this->tryNext($requestedPhar);
        }
    }

    private function localResolve(string $name): GitlabRepository {
        [$username, $project] = \explode('/', $name);
        $url                  = new Url(
            \sprintf('https://gitlab.com/api/v4/projects/%s%%2F%s/releases/', $username, $project)
        );

        $file = $this->fileDownloader->download($url);

        return new GitlabRepository(
            new JsonData($file->getContent())
        );
    }
}
