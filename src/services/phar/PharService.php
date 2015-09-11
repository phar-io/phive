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
         * @param PharDownloader $downloader
         * @param PharInstaller  $installer
         * @param PharRepository $repository
         * @param AliasResolver  $resolver
         */
        public function __construct(
            PharDownloader $downloader,
            PharInstaller $installer,
            PharRepository $repository,
            AliasResolver $resolver
        ) {
            $this->downloader = $downloader;
            $this->installer = $installer;
            $this->repository = $repository;
            $this->aliasResolver = $resolver;
        }

        /**
         * @param Url  $pharUrl
         *
         * @param      $destination
         * @param bool $makeCopy
         *
         * @return File
         * @throws PharRepositoryException
         * @throws VerificationFailedException
         */
        public function installByUrl(Url $pharUrl, $destination, $makeCopy = false) {
            $name = $this->getPharName($pharUrl);
            $version = $this->getPharVersion($pharUrl);
            if (!$this->repository->hasPhar($name, $version)) {
                $phar = new Phar($name, $version, $this->downloader->download($pharUrl));
                $this->repository->addPhar($phar);
            } else {
                $phar = $this->repository->getPhar($name, $version);
            }
            $this->install($phar, $destination, $makeCopy);
        }

        /**
         * @param PharAlias  $alias
         * @param string     $destination
         * @param bool       $makeCopy
         *
         * @throws InstallationFailedException
         * @throws ResolveException
         */
        public function installByAlias(PharAlias $alias, $destination, $makeCopy = false) {
            foreach ($this->aliasResolver->resolve($alias) as $repoUrl) {
                try {
                    //$repo = new PharIoRepository($repoUrl);
                    $repo = new PharIoRepository(__DIR__ . '/../../../phive.repository.xml');
                    $releases = $repo->getReleases($alias);
                    $this->installByUrl($releases->getLatest()->getUrl(), $destination, $makeCopy);
                    return;
                } catch (\Exception $e) {
                    // TODO catch only relevant exceptions
                    // TODO log failed installation attempt
                    continue;
                }
            }
            throw new InstallationFailedException('Installation failed');
        }

        /**
         * @param Phar   $phar
         * @param string $destination
         * @param bool   $makeCopy
         */
        private function install(Phar $phar, $destination, $makeCopy = false) {
            $destination = $destination . '/' . $phar->getName();
            $this->installer->install($phar->getFile(), $destination, $makeCopy);
            $this->repository->addUsage($phar, $destination);
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
                throw new DownloadFailedException(sprintf('Could not extract PHAR name from %s', $url));
            }

            return $matches[1];
        }

        /**
         * @param URl $url
         *
         * @return Version
         * @throws DownloadFailedException
         */
        private function getPharVersion(URl $url) {
            $filename = pathinfo((string)$url, PATHINFO_FILENAME);
            preg_match('/-([0-9]+.[0-9]+.[0-9]+.*)/', $filename, $matches);
            if (count($matches) !== 2) {
                throw new DownloadFailedException(sprintf('Could not extract PHAR version from %s', $url));
            }

            return new Version($matches[1]);
        }
    }

}

