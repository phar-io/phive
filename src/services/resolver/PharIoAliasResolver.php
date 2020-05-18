<?php declare(strict_types = 1);
namespace PharIo\Phive;

use PharIo\FileSystem\Filename;

/**
 * Resolves an alias to a list of Phar.io repository URLs
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
            $file   = $this->fileDownloader->download($source->getUrl());
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
                $filename = new Filename(\tempnam(\sys_get_temp_dir(), 'repo_'));
                $file->saveAs($filename);

                $repo = new PharIoRepository(
                    new XmlFile(
                        $filename,
                        'https://phar.io/repository',
                        'repository'
                    )
                );

                $file->getFilename()->delete();
                return $repo;
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
}
