<?php
namespace PharIo\Phive;

/**
 * Resolves an alias to a list of Phar.io repository URLs
 */
class PharIoAliasResolver extends AbstractRequestedPharResolver {

    /**
     * @var SourcesList
     */
    private $sources;

    /**
     * @var SourcesListFileLoader
     */
    private $loader;

    /**
     * @var FileDownloader
     */
    private $fileDownloader;

    /**
     * @param SourcesListFileLoader $loader
     * @param FileDownloader        $fileDownloader
     */
    public function __construct(SourcesListFileLoader $loader, FileDownloader $fileDownloader) {
        $this->loader = $loader;
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

        try {
            $source = $this->getSourcesList()->getSourceForAlias($requestedPhar->getAlias());
            $file = $this->fileDownloader->download($source->getUrl());
        } catch (SourcesListException $e) {
            return $this->tryNext($requestedPhar);
        }

        switch ($source->getType()) {
            case 'github':
                return new GithubRepository(
                    new JsonData($file->getContent())
                );
            case 'phar.io':
                $filename = new Filename(tempnam(sys_get_temp_dir(), 'repo_'));
                $file->saveAs($filename);
                return new PharIoRepository(
                    new XmlFile(
                        $filename,
                        'https://phar.io/repository',
                        'repository'
                    )
                );
        }

        return $this->tryNext($requestedPhar);
    }

    /**
     * @return SourcesList
     */
    protected function getSourcesList() {
        if ($this->sources === null) {
            $this->sources = $this->loader->load();
        }
        return $this->sources;
    }

}
