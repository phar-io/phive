<?php
namespace PharIo\Phive;

class GithubAliasResolver extends AbstractRequestedPharResolver {

    /**
     * @var FileDownloader
     */
    private $fileDownloader;

    /**
     * @var RateLimit
     */
    private $rateLimit;

    /**
     * @var HttpClient
     */
    private $httpClient;

    /**
     * @var Cli\Output
     */
    private $output;

    /**
     * @param FileDownloader $fileDownloader
     */
    public function __construct(HttpClient $httpClient, FileDownloader $fileDownloader, Cli\Output $output) {
        $this->fileDownloader = $fileDownloader;
        $this->httpClient = $httpClient;
        $this->output = $output;
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
        } catch (DownloadFailedException $e) {
            return $this->tryNext($requestedPhar);
        } catch (GithubAliasResolverException $e) {
            $this->output->writeWarning(
                sprintf('Github API Rate Limit exceeded - cannot resolve "%s"', $name)
            );
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

        $this->ensureWithinRateLimit();
        try {
            $file = $this->fileDownloader->download($url);
        } catch (DownloadFailedException $e) {
            $this->updateRateLimit();
            throw $e;
        }

        return new GithubRepository(
            new JsonData($file->getContent())
        );
    }

    /**
     * @throws GithubAliasResolverException
     */
    private function ensureWithinRateLimit() {
        $this->initRateLimit();
        if ($this->rateLimit->getRemaining() === 0) {
            throw new GithubAliasResolverException('Github API over rate limit');
        }
    }

    private function initRateLimit() {
        if ($this->rateLimit !== null) {
            return;
        }

        $response = $this->httpClient->head(new Url('https://api.github.com/rate_limit'));
        $this->rateLimit = $response->getRateLimit();
    }

    private function updateRateLimit() {
        if ($this->fileDownloader->hasRateLimit()) {
            $this->rateLimit = $this->fileDownloader->getRateLimit();
            return;
        }

        $this->rateLimit = new RateLimit(
            $this->rateLimit->getLimit(),
            $this->rateLimit->getRemaining() - 1,
            $this->rateLimit->getResetTime()
        );
    }

}
