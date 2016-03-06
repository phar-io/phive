<?php
namespace PharIo\Phive;

class PharService {

    /**
     * @var PharDownloader
     */
    private $downloader;

    /**
     * @var PharInstaller
     */
    private $installer;

    /**
     * @var PhiveInstallDB
     */
    private $repository;

    /**
     * @var AliasResolver
     */
    private $aliasResolver;

    /**
     * @var Cli\Output
     */
    private $output;

    /**
     * @var SourceRepositoryLoader
     */
    private $sourceRepositoryLoader;

    /**
     * @param PharDownloader         $downloader
     * @param PharInstaller          $installer
     * @param PhiveInstallDB         $repository
     * @param AliasResolver          $resolver
     * @param Cli\Output             $output
     * @param SourceRepositoryLoader $sourceRepositoryLoader
     */
    public function __construct(
        PharDownloader $downloader,
        PharInstaller $installer,
        PhiveInstallDB $repository,
        AliasResolver $resolver,
        Cli\Output $output,
        SourceRepositoryLoader $sourceRepositoryLoader
    ) {
        $this->downloader = $downloader;
        $this->installer = $installer;
        $this->repository = $repository;
        $this->aliasResolver = $resolver;
        $this->output = $output;
        $this->sourceRepositoryLoader = $sourceRepositoryLoader;
    }

    /**
     * @param RequestedPhar $requestedPhar
     * @param string        $destination
     * @param bool          $makeCopy
     */
    public function install(RequestedPhar $requestedPhar, $destination, $makeCopy = false) {
        try {
            $release = $this->getRelease($requestedPhar);

            $name = $this->getPharName($release->getUrl());
            $version = $this->getPharVersion($release->getUrl());
            if (!$this->repository->hasPhar($name, $version)) {
                $phar = new Phar($name, $version, $this->downloader->download($release));
                $this->repository->addPhar($phar);
            } else {
                $phar = $this->repository->getPhar($name, $version);
            }
            $destination = $destination . '/' . $phar->getName();
            $this->installer->install($phar->getFile(), $destination, $makeCopy);
            $this->repository->addUsage($phar, $destination);
        } catch (DownloadFailedException $e) {
            $this->output->writeError($e->getMessage());
        } catch (PharRepositoryException $e) {
            $this->output->writeError($e->getMessage());
        } catch (VerificationFailedException $e) {
            $this->output->writeError($e->getMessage());
        } catch (ResolveException $e) {
            $this->output->writeError($e->getMessage());
        }
    }

    /**
     * @param RequestedPhar $requestedPhar
     *
     * @throws DownloadFailedException
     * @throws ResolveException
     *
     * @return Release
     */
    private function getRelease(RequestedPhar $requestedPhar) {
        if ($requestedPhar->isAlias()) {
            return $this->resolveAlias($requestedPhar->getAlias());
        }

        return new Release(
            $this->getPharVersion($requestedPhar->getPharUrl()),
            $requestedPhar->getPharUrl(),
            null
        );
    }

    /**
     * @param PharAlias $alias
     *
     * @return Release
     * @throws InstallationFailedException
     * @throws ResolveException
     *
     */
    private function resolveAlias(PharAlias $alias) {
        foreach ($this->aliasResolver->resolve($alias) as $source) {
            try {
                $repo = $this->sourceRepositoryLoader->loadRepository($source);
                $releases = $repo->getReleasesByAlias($alias);
                return $releases->getLatest($alias->getVersionConstraint());
            } catch (ResolveException $e) {
                $this->output->writeWarning(
                    sprintf(
                        'Resolving alias %s with repository %s failed: %s',
                        $alias,
                        $source->getUrl(),
                        $e->getMessage()
                    )
                );
                continue;
            }
        }
        throw new ResolveException(sprintf('Could not resolve alias %s', $alias));
    }

    /**
     * @param Url $url
     *
     * @return Version
     * @throws DownloadFailedException
     */
    private function getPharVersion(Url $url) {
        $filename = pathinfo((string)$url, PATHINFO_FILENAME);
        preg_match('/-([0-9]+.[0-9]+.[0-9]+.*)/', $filename, $matches);
        if (count($matches) !== 2) {
            preg_match('/\/([0-9]+.[0-9]+.[0-9]+.*)\//', (string)$url, $matches);
        }
        if (count($matches) !== 2) {
            throw new DownloadFailedException(sprintf('Could not extract PHAR version from %s', $url));
        }

        return new Version($matches[1]);
    }

    /**
     * @param Url $url
     *
     * @return string
     * @throws DownloadFailedException
     */
    private function getPharName(Url $url) {
        $filename = pathinfo((string)$url, PATHINFO_FILENAME);
        preg_match('/(.*)-[0-9]+.[0-9]+.[0-9]+.*/', $filename, $matches);
        if (count($matches) !== 2) {
            $matches[1] = $filename;
        }

        return $matches[1];
    }
}
