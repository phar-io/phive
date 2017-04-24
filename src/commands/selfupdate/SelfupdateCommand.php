<?php
namespace PharIo\Phive;

use PharIo\FileSystem\Filename;
use PharIo\Phive\Cli;
use PharIo\Version\AnyVersionConstraint;
use PharIo\Version\Version;

class SelfupdateCommand implements Cli\Command {

    /**
     * @var PharDownloader
     */
    private $pharDownloader;

    /**
     * @var GithubAliasResolver
     */
    private $gitHubAliasResolver;

    /**
     * @var Environment
     */
    private $environment;

    /**
     * @var PhiveVersion
     */
    private $currentPhiveVersion;

    /**
     * @var Cli\Output
     */
    private $output;

    /**
     * SelfupdateCommand constructor.
     *
     * @param PharDownloader      $pharDownloader
     * @param GithubAliasResolver $gitHubAliasResolver
     * @param Environment         $environment
     * @param PhiveVersion        $currentPhiveVersion
     * @param Cli\Output          $output
     *
     * @internal param PharInstaller $pharInstaller
     */
    public function __construct(
        PharDownloader $pharDownloader, GithubAliasResolver $gitHubAliasResolver, Environment $environment, PhiveVersion $currentPhiveVersion, Cli\Output $output
    ) {
        $this->pharDownloader = $pharDownloader;
        $this->gitHubAliasResolver = $gitHubAliasResolver;
        $this->environment = $environment;
        $this->currentPhiveVersion = $currentPhiveVersion;
        $this->output = $output;
    }

    public function execute() {
        $requestedPhar = new RequestedPhar(
            new PharAlias('phar-io/phive'),
            new AnyVersionConstraint(),
            new AnyVersionConstraint()
        );

        $destination = new Filename($this->environment->getPhiveCommandPath());

        $repository = $this->gitHubAliasResolver->resolve($requestedPhar);
        $releases = $repository->getReleasesByRequestedPhar($requestedPhar);
        $release = $releases->getLatest($requestedPhar->getVersionConstraint());

        if (!$release->getVersion()->isGreaterThan(new Version($this->currentPhiveVersion->getVersion()))) {
            $this->output->writeInfo('You already have the newest version of PHIVE.');
            return;
        }

        try {
            $phar = $this->pharDownloader->download($release);
            $this->installPhivePhar($phar, $destination);
        } catch (DownloadFailedException $e) {
            $this->output->writeError('Downloading the new version failed: ' . $e->getMessage());
            return;
        } catch (InstallationFailedException $e) {
            $this->output->writeError('Installing the new version failed: ' . $e->getMessage());
            return;
        }

        $this->output->writeInfo(
            sprintf('PHIVE was successfully updated to version %s', $release->getVersion()->getVersionString())
        );
    }

    /**
     * @param Phar     $phar
     * @param Filename $destination
     *
     * @throws InstallationFailedException
     */
    private function installPhivePhar(Phar $phar, Filename $destination) {
        $tmpFilename = tempnam(sys_get_temp_dir(), 'phive_selfupdate_');

        if ($tmpFilename === false) {
            throw new InstallationFailedException(
                sprintf('Could not create temporary file in %s', sys_get_temp_dir())
            );
        }

        $tmpFilename = new Filename($tmpFilename);
        if (false === file_put_contents($tmpFilename->asString(), $phar->getFile()->getContent())) {
            throw new InstallationFailedException(
                sprintf('Could not write to %s', $tmpFilename->asString())
            );
        }

        if (false === copy($tmpFilename, $destination->asString())) {
            throw new InstallationFailedException(
                sprintf('Could not copy temporary file to %s', $destination->asString())
            );
        }

        if (false === chmod($destination, 0755)) {
            throw new InstallationFailedException(
                sprintf('Could not make %s executable, please fix manually', $destination->asString())
            );
        }

        if (false === unlink($tmpFilename)) {
            throw new InstallationFailedException(
                sprintf('Could not remove temporary file %s, please fix manually', $tmpFilename->asString())
            );
        }
    }
}
