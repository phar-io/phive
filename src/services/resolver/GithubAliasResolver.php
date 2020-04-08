<?php declare(strict_types = 1);
namespace PharIo\Phive;

class GithubAliasResolver extends AbstractRequestedPharResolver {
    /** @var FileDownloader */
    private $fileDownloader;

    /** @var null|RateLimit */
    private $rateLimit;

    /** @var HttpClient */
    private $httpClient;

    /** @var Cli\Output */
    private $output;

    public function __construct(HttpClient $httpClient, FileDownloader $fileDownloader, Cli\Output $output) {
        $this->fileDownloader = $fileDownloader;
        $this->httpClient     = $httpClient;
        $this->output         = $output;
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
        } catch (GithubAliasResolverException $e) {
            $this->output->writeWarning(
                \sprintf('Github API Rate Limit exceeded - cannot resolve "%s"', $name)
            );

            return $this->tryNext($requestedPhar);
        }
    }

    private function localResolve(string $name): GithubRepository {
        [$username, $project] = \explode('/', $name);
        $url                  = new Url(
            \sprintf('https://api.github.com/repos/%s/%s/releases', $username, $project)
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
    private function ensureWithinRateLimit(): void {
        $this->initRateLimit();

        if ($this->rateLimit->getRemaining() === 0) {
            throw new GithubAliasResolverException('Github API over rate limit');
        }
    }

    /** @psalm-assert !null $this->rateLimit */
    private function initRateLimit(): void {
        if ($this->rateLimit !== null) {
            return;
        }

        $response        = $this->httpClient->head(new Url('https://api.github.com/user'));
        $this->rateLimit = $response->getRateLimit();
    }

    private function updateRateLimit(): void {
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
