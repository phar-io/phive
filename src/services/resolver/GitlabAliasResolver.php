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

        if (strpos($name, '/') === false) {
            return $this->tryNext($requestedPhar);
        }

        try {
            return $this->localResolve($name);
        } catch (DownloadFailedException $e) {
            return $this->tryNext($requestedPhar);
        }
    }

    private function localResolve(string $name): GitlabRepository {
        [$username, $project] = explode('/', $name);
        $url                  = new Url(
            sprintf('https://gitlab.com/api/v4/projects/%s%%2F%s/releases/', $username, $project)
        );

        $file = $this->fileDownloader->download($url);

        return new GitlabRepository(
            new JsonData($file->getContent())
        );
    }
}
