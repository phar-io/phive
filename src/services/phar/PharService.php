<?php
namespace PharIo\Phive {

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
         * @var PharRepository
         */
        private $repository;

        /**
         * @var AliasResolver
         */
        private $aliasResolver;

        /**
         * @var Output
         */
        private $output;

        /**
         * @param PharDownloader $downloader
         * @param PharInstaller  $installer
         * @param PharRepository $repository
         * @param AliasResolver  $resolver
         * @param Output         $output
         */
        public function __construct(
            PharDownloader $downloader,
            PharInstaller $installer,
            PharRepository $repository,
            AliasResolver $resolver,
            Output $output
        ) {
            $this->downloader = $downloader;
            $this->installer = $installer;
            $this->repository = $repository;
            $this->aliasResolver = $resolver;
            $this->output = $output;
        }

        /**
         * @param RequestedPhar $requestedPhar
         * @param string        $destination
         * @param bool          $makeCopy
         *
         * @return File
         * @throws DownloadFailedException
         * @throws PharRepositoryException
         * @throws VerificationFailedException
         *
         */
        public function install(RequestedPhar $requestedPhar, $destination, $makeCopy = false) {
            if ($requestedPhar->isAlias()) {
                $pharUrl = $this->resolveAlias($requestedPhar->getAlias());
            } else {
                $pharUrl = $requestedPhar->getPharUrl();
            }

            $name = $this->getPharName($pharUrl);
            $version = $this->getPharVersion($pharUrl);
            if (!$this->repository->hasPhar($name, $version)) {
                $phar = new Phar($name, $version, $this->downloader->download($pharUrl));
                $this->repository->addPhar($phar);
            } else {
                $phar = $this->repository->getPhar($name, $version);
            }
            $destination = $destination . '/' . $phar->getName();
            $this->installer->install($phar->getFile(), $destination, $makeCopy);
            $this->repository->addUsage($phar, $destination);
        }

        /**
         * @param PharAlias $alias
         *
         * @return Url
         * @throws InstallationFailedException
         * @throws ResolveException
         *
         */
        private function resolveAlias(PharAlias $alias) {
            foreach ($this->aliasResolver->resolve($alias) as $repoUrl) {
                try {
                    $repo = new PharIoRepository($repoUrl);
                    $releases = $repo->getReleases($alias);
                    return $releases->getLatest($alias->getVersionConstraint())->getUrl();
                } catch (ResolveException $e) {
                    $this->output->writeWarning(
                        sprintf('Resolving alias %s with repository %s failed: %s', $alias, $repoUrl, $e->getMessage())
                    );
                    continue;
                }
            }
            throw new ResolveException(sprintf('Could not resolve alias %s', $alias));
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
    }

}

