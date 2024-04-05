<?php declare(strict_types=1);
/*
 * This file is part of Phive.
 *
 * Copyright (c) Arne Blankerts <arne@blankerts.de>, Sebastian Heuer <sebastian@phpeople.de> and contributors
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */
namespace PharIo\Phive;

use function explode;
use function sprintf;
use function strpos;

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

        if (strpos($name, '/') === false) {
            return $this->tryNext($requestedPhar);
        }

        try {
            return $this->localResolve($name);
        } catch (DownloadFailedException $e) {
            return $this->tryNext($requestedPhar);
        } catch (GithubAliasResolverException $e) {
            $this->output->writeWarning(
                sprintf('GitHub API Rate Limit exceeded - cannot resolve "%s"', $name)
            );

            return $this->tryNext($requestedPhar);
        }
    }

    private function localResolve(string $name): GithubRepository {
        [$username, $project] = explode('/', $name);
        $url                  = new Url(
            sprintf('https://api.github.com/repos/%s/%s/releases?per_page=100', $username, $project)
        );

        $this->ensureWithinRateLimit();

        try {
            $file = $this->fileDownloader->download($url);
        } catch (DownloadFailedException $e) {
            $this->updateRateLimit($this->rateLimit);

            throw $e;
        }

        return new GithubRepository(
            new JsonData($file->getContent())
        );
    }

    /**
     * @throws GithubAliasResolverException
     *
     * @psalm-assert !null $this->rateLimit
     */
    private function ensureWithinRateLimit(): void {
        $this->initRateLimit();

        if ($this->rateLimit->getRemaining() === 0) {
            throw new GithubAliasResolverException('GitHub API over rate limit');
        }
    }

    /** @psalm-assert !null $this->rateLimit */
    private function initRateLimit(): void {
        if ($this->rateLimit !== null) {
            return;
        }

        $response        = $this->httpClient->get(new Url('https://api.github.com/rate_limit'));
        $this->rateLimit = $response->getRateLimit();
    }

    private function updateRateLimit(RateLimit $previousRateLimit): void {
        if ($this->fileDownloader->hasRateLimit()) {
            $this->rateLimit = $this->fileDownloader->getRateLimit();

            return;
        }

        $this->rateLimit = new RateLimit(
            $previousRateLimit->getLimit(),
            $previousRateLimit->getRemaining() - 1,
            $previousRateLimit->getResetTime()
        );
    }
}
