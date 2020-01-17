<?php declare(strict_types = 1);
namespace PharIo\Phive;

use PharIo\Phive\Cli\ConsoleTable;

class OutdatedCommand implements Cli\Command {

    /** @var PhiveXmlConfig */
    private $phiveXmlConfig;

    /** @var Cli\Output */
    private $output;

    /** @var RequestedPharResolverService */
    private $pharResolver;

    /** @var ReleaseSelector */
    private $selector;

    public function __construct(RequestedPharResolverService $pharResolver, ReleaseSelector $selector, PhiveXmlConfig $phiveXmlConfig, Cli\Output $output) {
        $this->pharResolver = $pharResolver;
        $this->selector = $selector;
        $this->phiveXmlConfig = $phiveXmlConfig;
        $this->output = $output;
    }

    public function execute(): void {
        $outdated = 0;
        $table = new ConsoleTable(['Name', 'Version Constraint', 'Installed', 'Available']);

        foreach($this->phiveXmlConfig->getPhars() as $phar) {
            if (!$phar->isInstalled()) {
                continue;
            }

            if ($phar->hasUrl() || Url::isUrl($phar->getName())) {
                $this->output->writeWarning(
                    sprintf(
                        'Phar "%s" installed via URL - cannot check for newer versions',
                        $phar->getName()
                    )
                );
                continue;
            }

            $latest = $this->resolveToRelease(new RequestedPhar(
                new PharAlias($phar->getName()),
                $phar->getVersionConstraint(),
                $phar->getVersionConstraint(),
                $phar->getLocation(),
                $phar->isCopy()
            ));

            if (!$latest->getVersion()->isGreaterThan($phar->getInstalledVersion())) {
                continue;
            }

            $table->addRow([
                $phar->getName(),
                $phar->getVersionConstraint()->asString(),
                $phar->getInstalledVersion()->getVersionString(),
                $latest->getVersion()->getVersionString()
            ]);

            $outdated++;
        }

        if ($outdated === 0) {
            $this->output->writeText('Congrats, no outdated phars found');
            return;
        }

        $this->output->writeText(
            sprintf('Found %d outdated PHARs in phive.xml:', $outdated)
            . "\n\n" .
            $table->asString()
        );
    }

    private function resolveToRelease(RequestedPhar $requestedPhar): SupportedRelease {
        $repository = $this->pharResolver->resolve($requestedPhar);
        $releases = $repository->getReleasesByRequestedPhar($requestedPhar);

        return $this->selector->select($releases, $requestedPhar->getVersionConstraint(), true);
    }

}
