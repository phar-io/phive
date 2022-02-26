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

/**
 * Resolves an alias to a list of Phar.io repository URLs.
 */
class PharIoAliasResolver extends AbstractRequestedPharResolver {
    /** @var null|SourcesList */
    private $sources;

    /** @var SourcesListFileLoader */
    private $loader;

    /** @var FileDownloader */
    private $fileDownloader;

    public function __construct(SourcesListFileLoader $loader, FileDownloader $fileDownloader) {
        $this->loader         = $loader;
        $this->fileDownloader = $fileDownloader;
    }

    public function resolve(RequestedPhar $requestedPhar): SourceRepository {
        if (!$requestedPhar->hasAlias()) {
            return $this->tryNext($requestedPhar);
        }

        try {
            $source = $this->getSourcesList()->getSourceForAlias($requestedPhar->getAlias());
            $file   = $this->fileDownloader->download($this->getUrl($source));
        } catch (SourcesListException $e) {
            return $this->tryNext($requestedPhar);
        }

        switch ($source->getType()) {
            case 'github':
                return new GithubRepository(
                    new JsonData($file->getContent())
                );
            case 'gitlab':
                return new GitlabRepository(
                    new JsonData($file->getContent())
                );
            case 'phar.io':
                try {
                    return new PharIoRepository(
                        XmlFile::fromFile($file)
                    );
                } catch (InvalidXmlException $e) {
                    break;
                }
        }

        return $this->tryNext($requestedPhar);
    }

    /** @psalm-assert !null $this->sources */
    protected function getSourcesList(): SourcesList {
        if ($this->sources === null) {
            $this->sources = $this->loader->load();
        }

        return $this->sources;
    }

    private function getUrl(Source $source): Url {
        $url = $source->getUrl();

        if ($source->getType() === 'github') {
            $url = $url->withParams(['per_page' => 100]);
        }

        return $url;
    }
}
