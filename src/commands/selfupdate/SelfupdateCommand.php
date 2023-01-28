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

use function chmod;
use function copy;
use function file_put_contents;
use function sprintf;
use function sys_get_temp_dir;
use function tempnam;
use function unlink;
use PharIo\FileSystem\Filename;
use PharIo\Version\AnyVersionConstraint;
use PharIo\Version\Version;

class SelfupdateCommand implements Cli\Command {
    /** @var PharDownloader */
    private $pharDownloader;

    /** @var GithubAliasResolver */
    private $gitHubAliasResolver;

    /** @var Environment */
    private $environment;

    /** @var PhiveVersion */
    private $currentPhiveVersion;

    /** @var Cli\Output */
    private $output;

    /** @var ReleaseSelector */
    private $selector;

    public function __construct(
        PharDownloader $pharDownloader,
        GithubAliasResolver $gitHubAliasResolver,
        Environment $environment,
        PhiveVersion $currentPhiveVersion,
        Cli\Output $output,
        ReleaseSelector $selector
    ) {
        $this->pharDownloader      = $pharDownloader;
        $this->gitHubAliasResolver = $gitHubAliasResolver;
        $this->environment         = $environment;
        $this->currentPhiveVersion = $currentPhiveVersion;
        $this->output              = $output;
        $this->selector            = $selector;
    }

    public function execute(): void {
        $requestedPhar = new RequestedPhar(
            new PharAlias('phar-io/phive'),
            new AnyVersionConstraint(),
            new AnyVersionConstraint()
        );

        $destination = new Filename($this->environment->getPhiveCommandPath());

        $repository = $this->gitHubAliasResolver->resolve($requestedPhar);
        $releases   = $repository->getReleasesByRequestedPhar($requestedPhar);
        $release    = $this->selector->select(
            $requestedPhar->getIdentifier(),
            $releases,
            $requestedPhar->getVersionConstraint(),
            false
        );

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
     * @throws InstallationFailedException
     */
    private function installPhivePhar(Phar $phar, Filename $destination): void {
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

        if (false === copy($tmpFilename->asString(), $destination->asString())) {
            throw new InstallationFailedException(
                sprintf('Could not copy temporary file to %s', $destination->asString())
            );
        }

        if (false === chmod($destination->asString(), 0755)) {
            throw new InstallationFailedException(
                sprintf('Could not make %s executable, please fix manually', $destination->asString())
            );
        }

        if (false === unlink($tmpFilename->asString())) {
            throw new InstallationFailedException(
                sprintf('Could not remove temporary file %s, please fix manually', $tmpFilename->asString())
            );
        }
    }
}
