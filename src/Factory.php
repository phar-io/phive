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

use const PHP_OS;
use function getcwd;
use PharIo\GnuPG\Factory as GnuPGFactory;
use PharIo\Version\VersionConstraintParser;

class Factory {
    /** @var null|CurlConfig */
    private $curlConfig;

    /** @var null|PhiveVersion */
    private $version;

    /** @var Cli\Request */
    private $request;

    /** @var null|Environment */
    private $environment;

    /** @var null|PharRegistry */
    private $registry;

    public function __construct(Cli\Request $request, ?PhiveVersion $version = null) {
        $this->request = $request;
        $this->version = $version;
    }

    public function getRunner(): Cli\Runner {
        return new Cli\Runner(
            $this->getCommandLocator(),
            $this->getOutput(),
            $this->getPhiveVersion(),
            $this->getEnvironment(),
            $this->getMigrationService(),
            $this->request
        );
    }

    public function getVersionCommand(): VersionCommand {
        return new VersionCommand;
    }

    public function getHelpCommand(): HelpCommand {
        return new HelpCommand(
            $this->getEnvironment(),
            $this->getOutput()
        );
    }

    public function getSkelCommand(): SkelCommand {
        return new SkelCommand(
            new SkelCommandConfig($this->request->parse(new SkelContext()), getcwd()),
            $this->getOutput(),
            $this->getPhiveVersion()
        );
    }

    public function getUpdateRepositoryListCommand(): UpdateRepositoryListCommand {
        return new UpdateRepositoryListCommand($this->getRemoteSourcesListFileLoader());
    }

    public function getRemoveCommand(): RemoveCommand {
        return new RemoveCommand(
            new RemoveCommandConfig($this->request->parse(new RemoveContext()), $this->getTargetDirectoryLocator()),
            $this->getPharRegistry(),
            $this->getOutput(),
            $this->getPhiveXmlConfig($this->request->getOptions()->hasOption('global')),
            $this->getRemovalService()
        );
    }

    public function getResetCommand(): ResetCommand {
        return new ResetCommand(
            new ResetCommandConfig($this->request->parse(new ResetContext())),
            $this->getPharRegistry(),
            $this->getEnvironment(),
            $this->getPharInstaller()
        );
    }

    public function getInstallCommand(): InstallCommand {
        return new InstallCommand(
            new InstallCommandConfig(
                $this->request->parse(new InstallContext()),
                $this->getPhiveXmlConfig($this->request->getOptions()->hasOption('global')),
                $this->getEnvironment(),
                $this->getTargetDirectoryLocator()
            ),
            $this->getInstallService(),
            $this->getRequestedPharResolverBuilder()->build($this->getLocalFirstResolvingStrategy()),
            $this->getReleaseSelector()
        );
    }

    public function getUpdateCommand(): UpdateCommand {
        $config = new UpdateCommandConfig(
            $this->request->parse(new UpdateContext()),
            $this->getPhiveXmlConfig($this->request->getOptions()->hasOption('global')),
            $this->getTargetDirectoryLocator()
        );

        $resolvingStrategy = $config->preferOffline() ? $this->getLocalFirstResolvingStrategy() : $this->getRemoteFirstResolvingStrategy();

        return new UpdateCommand(
            $config,
            $this->getInstallService(),
            $this->getRequestedPharResolverBuilder()->build($resolvingStrategy),
            $this->getPhiveXmlConfig($this->request->getOptions()->hasOption('global')),
            $this->getReleaseSelector()
        );
    }

    public function getListCommand(): ListCommand {
        return new ListCommand(
            $this->getSourcesList(),
            $this->getLocalSourcesList(),
            $this->getOutput()
        );
    }

    public function getPurgeCommand(): PurgeCommand {
        return new PurgeCommand(
            $this->getPharRegistry(),
            $this->getOutput()
        );
    }

    public function getComposerCommand(): ComposerCommand {
        return new ComposerCommand(
            new ComposerCommandConfig(
                $this->request->parse(new ComposerContext()),
                $this->getPhiveXmlConfig($this->request->getOptions()->hasOption('global')),
                $this->getEnvironment(),
                $this->getTargetDirectoryLocator(),
                $this->getEnvironment()->getWorkingDirectory()
            ),
            $this->getComposerService(),
            $this->getInstallService(),
            $this->getConsoleInput(),
            $this->getRequestedPharResolverBuilder()->build(
                $this->getLocalFirstResolvingStrategy()
            ),
            $this->getReleaseSelector()
        );
    }

    public function getStatusCommand(): StatusCommand {
        return new StatusCommand(
            new StatusCommandConfig(
                $this->request->parse(new StatusContext()),
                $this->getPhiveXmlConfig($this->request->getOptions()->hasOption('global')),
                $this->getPharRegistry()
            ),
            $this->getPharRegistry(),
            $this->getOutput()
        );
    }

    public function getSelfupdateCommand(): SelfupdateCommand {
        return new SelfupdateCommand(
            $this->getPharDownloader(),
            $this->getGithubAliasResolver(),
            $this->getEnvironment(),
            $this->getPhiveVersion(),
            $this->getOutput(),
            $this->getReleaseSelector()
        );
    }

    public function getOutdatedCommand(): OutdatedCommand {
        return new OutdatedCommand(
            new OutdatedConfig($this->request->parse(new OutdatedContext())),
            $this->getRequestedPharResolverBuilder()->build($this->getRemoteFirstResolvingStrategy()),
            $this->getReleaseSelector(),
            $this->getPhiveXmlConfig($this->request->getOptions()->hasOption('global')),
            $this->getOutput()
        );
    }

    public function getMigrateCommand(): MigrateCommand {
        return new MigrateCommand(
            $this->getMigrationService(),
            new MigrateCommandConfig($this->request->parse(new MigrateContext())),
            $this->getOutput()
        );
    }

    public function getDefaultCommand(): DefaultCommand {
        return new DefaultCommand(
            $this->getVersionCommand(),
            $this->getHelpCommand(),
            new DefaultCommandConfig($this->request->getOptions())
        );
    }

    public function getOutput(): Cli\Output {
        return (new Cli\OutputLocator(new Cli\OutputFactory()))->getOutput(
            $this->getEnvironment(),
            !$this->request->getOptions()->hasOption('no-progress')
        );
    }

    public function getRemoteSourcesListFileLoader(): RemoteSourcesListFileLoader {
        return new RemoteSourcesListFileLoader(
            $this->getConfig()->getSourcesListUrl(),
            $this->getConfig()->getPharIoRepositories(),
            $this->getFileDownloader(),
            $this->getOutput(),
            $this->getConfig()->getMaxAgeForSourcesList()
        );
    }

    public function getLocalSourcesListFileLoader(): LocalSourcesListFileLoader {
        return new LocalSourcesListFileLoader(
            $this->getConfig()->getLocalRepositories()
        );
    }

    public function getConfig(): Config {
        return new Config(
            $this->getEnvironment(),
            $this->request->getOptions()
        );
    }

    public function getFileDownloader(): FileDownloader {
        return new FileDownloader(
            $this->getRetryingHttpClient(),
            $this->getFileStorageCacheBackend()
        );
    }

    public function getHttpClient(): CurlHttpClient {
        return new CurlHttpClient(
            $this->getCurlConfig(),
            $this->getHttpProgressRenderer(),
            new Curl()
        );
    }

    public function getAuthConfig(): AuthConfig {
        return new CompositeAuthConfig([
            new EnvironmentAuthConfig($this->getEnvironment()),
            new AuthXmlConfig(new XmlFile(
                $this->getAuthXmlConfigFileLocator()->getFile(false),
                'https://phar.io/auth',
                'auth'
            )),
            new AuthXmlConfig(new XmlFile(
                $this->getAuthXmlConfigFileLocator()->getFile(true),
                'https://phar.io/auth',
                'auth'
            ))
        ]);
    }

    /** @psalm-assert !null $this->registry */
    public function getPharRegistry(): PharRegistry {
        if ($this->registry === null) {
            $this->registry = new PharRegistry(
                new XmlFile(
                    $this->getConfig()->getRegistry(),
                    'https://phar.io/phive/installdb',
                    'phars'
                ),
                $this->getConfig()->getPharsDirectory()
            );
        }

        return $this->registry;
    }

    public function getRequestedPharResolverService(): RequestedPharResolverService {
        return new RequestedPharResolverService();
    }

    public function getGithubAliasResolver(): GithubAliasResolver {
        return new GithubAliasResolver(
            $this->getHttpClient(),
            $this->getFileDownloader(),
            $this->getOutput()
        );
    }

    public function getGitlabAliasResolver(): GitlabAliasResolver {
        return new GitlabAliasResolver(
            $this->getFileDownloader()
        );
    }

    private function getTargetDirectoryLocator(): TargetDirectoryLocator {
        return new TargetDirectoryLocator(
            $this->getConfig(),
            $this->getPhiveXmlConfig($this->request->getOptions()->hasOption('global')),
            $this->request->getOptions()
        );
    }

    private function getCommandLocator(): CommandLocator {
        return new CommandLocator($this);
    }

    /** @psalm-assert !null $this->version */
    private function getPhiveVersion(): PhiveVersion {
        if ($this->version === null) {
            $this->version = new GitAwarePhiveVersion($this->getGit());
        }

        return $this->version;
    }

    private function getInstallService(): InstallService {
        return new InstallService(
            $this->getPhiveXmlConfig($this->request->getOptions()->hasOption('global')),
            $this->getPharInstaller(),
            $this->getPharRegistry(),
            $this->getPharService(),
            $this->getCompatibilityService()
        );
    }

    private function getGit(): Git {
        return new Git($this->getEnvironment()->getWorkingDirectory());
    }

    private function getEnvironment(): Environment {
        if (null === $this->environment) {
            $this->environment = (new EnvironmentLocator())->getEnvironment(PHP_OS);
        }

        return $this->environment;
    }

    private function getRetryingHttpClient(): RetryingHttpClient {
        return new RetryingHttpClient(
            $this->getOutput(),
            $this->getHttpClient(),
            5
        );
    }

    private function getHttpProgressRenderer(): HttpProgressRenderer {
        return new HttpProgressRenderer($this->getOutput());
    }

    private function getPharService(): PharService {
        return new PharService(
            $this->getPharRegistry(),
            $this->getPharDownloader()
        );
    }

    private function getPharDownloader(): PharDownloader {
        return new PharDownloader(
            $this->getRetryingHttpClient(),
            $this->getGnupgSignatureVerifier(),
            $this->getChecksumService(),
            $this->getPharRegistry()
        );
    }

    private function getGnupgSignatureVerifier(): SignatureVerifier {
        return new GnupgSignatureVerifier($this->getGnupg(), $this->getKeyService());
    }

    private function getGnupg(): GnuPG {
        $home = $this->getConfig()->getGPGDirectory();
        $home->ensureExists(0700);

        $bin = $this->getConfig()->getGPGBinaryPath();

        return new GnuPG(
            (new GnuPGFactory($bin))->createGnuPG($home),
            $home
        );
    }

    private function getKeyService(): KeyService {
        return new KeyService(
            $this->getPgpKeyDownloader(),
            $this->getGnupgKeyImporter(),
            $this->getConfig()->getTrusted(),
            $this->getOutput(),
            $this->getConsoleInput()
        );
    }

    private function getPgpKeyDownloader(): GnupgKeyDownloader {
        return new GnupgKeyDownloader(
            $this->getRingdownCurlHttpClient(),
            include __DIR__ . '/../conf/pgp-keyservers.php',
            $this->getPublicKeyReader(),
            $this->getOutput()
        );
    }

    private function getGnupgKeyImporter(): KeyImporter {
        return new GnupgKeyImporter($this->getGnupg());
    }

    private function getConsoleInput(): Cli\ConsoleInput {
        return new Cli\ConsoleInput($this->getOutput());
    }

    private function getChecksumService(): ChecksumService {
        return new ChecksumService();
    }

    private function getPharInstaller(): PharInstaller {
        return $this->getPharInstallerLocator()->getPharInstaller($this->getEnvironment());
    }

    private function getPhiveXmlConfig(bool $global): PhiveXmlConfig {
        if ($global) {
            return new GlobalPhiveXmlConfig(
                new XmlFile(
                    $this->getConfig()->getGlobalInstallation(),
                    'https://phar.io/phive',
                    'phive'
                ),
                new VersionConstraintParser()
            );
        }

        return new LocalPhiveXmlConfig(
            new XmlFile(
                $this->getPhiveXmlConfigFileLocator()->getFile(),
                'https://phar.io/phive',
                'phive'
            ),
            new VersionConstraintParser(),
            $this->getEnvironment()
        );
    }

    private function getPhiveXmlConfigFileLocator(): PhiveXmlConfigFileLocator {
        return new PhiveXmlConfigFileLocator(
            $this->getEnvironment(),
            $this->getConfig(),
            $this->getOutput()
        );
    }

    private function getAuthXmlConfigFileLocator(): AuthXmlConfigFileLocator {
        return new AuthXmlConfigFileLocator(
            $this->getEnvironment(),
            $this->getConfig(),
            $this->getOutput()
        );
    }

    private function getComposerService(): ComposerService {
        return new ComposerService($this->getSourcesList());
    }

    private function getPharInstallerLocator(): PharInstallerLocator {
        return new PharInstallerLocator(new PharInstallerFactory($this));
    }

    private function getFileStorageCacheBackend(): FileStorageCacheBackend {
        return new FileStorageCacheBackend($this->getConfig()->getHttpCacheDirectory());
    }

    private function getRequestedPharResolverBuilder(): RequestedPharResolverServiceBuilder {
        return new RequestedPharResolverServiceBuilder($this);
    }

    private function getSourcesList(): SourcesList {
        return $this->getRemoteSourcesListFileLoader()->load();
    }

    private function getLocalSourcesList(): SourcesList {
        return $this->getLocalSourcesListFileLoader()->load();
    }

    private function getLocalFirstResolvingStrategy(): LocalFirstResolvingStrategy {
        return new LocalFirstResolvingStrategy($this->getRequestedPharResolverFactory());
    }

    private function getRemoteFirstResolvingStrategy(): RemoteFirstResolvingStrategy {
        return new RemoteFirstResolvingStrategy($this->getRequestedPharResolverFactory());
    }

    private function getRequestedPharResolverFactory(): RequestedPharResolverFactory {
        return new RequestedPharResolverFactory($this);
    }

    private function getCompatibilityService(): CompatibilityService {
        return new CompatibilityService(
            $this->getOutput(),
            $this->getConsoleInput()
        );
    }

    private function getMigrationService(): MigrationService {
        return new MigrationService(
            new MigrationFactory($this, $this->getConsoleInput())
        );
    }

    /** @psalm-assert !null $this->curlConfig */
    private function getCurlConfig(): CurlConfig {
        if ($this->curlConfig === null) {
            $this->curlConfig = (new CurlConfigBuilder($this->getEnvironment(), $this->getPhiveVersion(), $this->getAuthConfig()))->build();
        }

        return $this->curlConfig;
    }

    private function getReleaseSelector(): ReleaseSelector {
        return new ReleaseSelector($this->getOutput());
    }

    private function getRingdownCurlHttpClient(): RingdownCurlHttpClient {
        return new RingdownCurlHttpClient(
            $this->getHttpClient(),
            $this->getCurlConfig(),
            $this->getOutput()
        );
    }

    private function getTemporaryGnupg(): GnuPG {
        $home = $this->getConfig()->getTemporaryWorkingDirectory();
        $home->ensureExists(0700);

        $bin = $this->getConfig()->getGPGBinaryPath();

        return new GnuPG(
            (new GnuPGFactory($bin))->createGnuPG($home),
            $home
        );
    }

    private function getPublicKeyReader(): PublicKeyReader {
        return new PublicKeyReader(
            $this->getTemporaryGnupg(),
            $this->getConfig()->getTemporaryWorkingDirectory()
        );
    }

    private function getRemovalService(): RemovalService {
        return new RemovalService(
            $this->getEnvironment(),
            $this->getOutput()
        );
    }
}
