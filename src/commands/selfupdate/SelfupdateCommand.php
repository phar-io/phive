<?php
namespace PharIo\Phive;

use PharIo\Phive\Cli;

class SelfupdateCommand implements Cli\Command {

    /**
     * @var PharDownloader
     */
    private $pharDownloader;

    /**
     * @var SourceRepositoryLoader
     */
    private $sourceRepositoryLoader;

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
     * @var PharInstaller
     */
    private $pharInstaller;

    /**
     * SelfupdateCommand constructor.
     *
     * @param PharDownloader         $pharDownloader
     * @param SourceRepositoryLoader $sourceRepositoryLoader
     * @param GithubAliasResolver    $gitHubAliasResolver
     * @param Environment            $environment
     * @param PhiveVersion           $currentPhiveVersion
     * @param Cli\Output             $output
     * @param PharInstaller          $pharInstaller
     */
    public function __construct(
        PharDownloader $pharDownloader,
        SourceRepositoryLoader $sourceRepositoryLoader,
        GithubAliasResolver $gitHubAliasResolver,
        Environment $environment,
        PhiveVersion $currentPhiveVersion,
        Cli\Output $output,
        PharInstaller $pharInstaller
    ) {
        $this->pharDownloader = $pharDownloader;
        $this->sourceRepositoryLoader = $sourceRepositoryLoader;
        $this->gitHubAliasResolver = $gitHubAliasResolver;
        $this->environment = $environment;
        $this->currentPhiveVersion = $currentPhiveVersion;
        $this->output = $output;
        $this->pharInstaller = $pharInstaller;
    }

    /**
     *
     */
    public function execute() {
        $requestedPhar = new RequestedPharAlias(new PharAlias('phar-io/phive', new AnyVersionConstraint()));

        $destination = new Filename($this->environment->getPhiveCommandPath());

        $source = $this->gitHubAliasResolver->resolve($requestedPhar->getAlias());
        $repo = $this->sourceRepositoryLoader->loadRepository($source[0]);
        $releases = $repo->getReleasesByAlias($requestedPhar->getAlias());
        $release = $releases->getLatest($requestedPhar->getAlias()->getVersionConstraint());

        if (!$release->getVersion()->isGreaterThan(new Version($this->currentPhiveVersion->getVersion()))) {
            $this->output->writeInfo('You already have the newest version of PHIVE.');
            return;
        }

        $phar = $this->pharDownloader->download($release);
        $this->pharInstaller->install($phar->getFile(), $destination, true);

        $this->output->writeInfo(
            sprintf('PHIVE was successfully updated to version %s', $release->getVersion()->getVersionString())
        );
    }
}
