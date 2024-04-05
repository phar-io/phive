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

use const JSON_PRETTY_PRINT;
use function count;
use function file_put_contents;
use function json_encode;
use function sprintf;
use DOMDocument;
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

    /** @var OutdatedConfig */
    private $outdatedConfig;

    public function __construct(OutdatedConfig $outdatedConfig, RequestedPharResolverService $pharResolver, ReleaseSelector $selector, PhiveXmlConfig $phiveXmlConfig, Cli\Output $output) {
        $this->pharResolver   = $pharResolver;
        $this->selector       = $selector;
        $this->phiveXmlConfig = $phiveXmlConfig;
        $this->output         = $output;
        $this->outdatedConfig = $outdatedConfig;
    }

    public function execute(): void {
        $output = $this->renderOutput(
            $this->gatherOutdated()
        );

        if ($this->outdatedConfig->saveToFile()) {
            $this->writeToFile($output);

            return;
        }

        $this->output->writeText($output);
    }

    private function renderOutput(array $outdated): string {
        if ($this->outdatedConfig->wantsJson()) {
            return $this->renderJsonOutput($outdated);
        }

        if ($this->outdatedConfig->wantsXml()) {
            return $this->renderXmlOutput($outdated);
        }

        return $this->renderCliOutput($outdated);
    }

    private function renderCliOutput(array $outdated): string {
        if (count($outdated) === 0) {
            return 'Congrats, no outdated phars found';
        }

        $table = new ConsoleTable(['Name', 'Version Constraint', 'Installed', 'Available']);

        foreach ($outdated as $entry) {
            $table->addRow([
                $entry['name'],
                $entry['constraint'],
                $entry['installed'],
                $entry['available']
            ]);
        }

        return sprintf(
            "Found %d outdated PHARs in phive.xml:\n\n%s",
            count($outdated),
            $table->asString()
        );
    }

    private function gatherOutdated(): array {
        $outdated = [];

        foreach ($this->phiveXmlConfig->getPhars() as $phar) {
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

            $outdated[] = [
                'name'       => $phar->getName(),
                'constraint' => $phar->getVersionConstraint()->asString(),
                'installed'  => $phar->getInstalledVersion()->getVersionString(),
                'available'  => $latest->getVersion()->getVersionString()
            ];
        }

        return $outdated;
    }

    private function resolveToRelease(RequestedPhar $requestedPhar): SupportedRelease {
        $repository = $this->pharResolver->resolve($requestedPhar);
        $releases   = $repository->getReleasesByRequestedPhar($requestedPhar);

        return $this->selector->select(
            $requestedPhar->getIdentifier(),
            $releases,
            $requestedPhar->getVersionConstraint(),
            true
        );
    }

    private function renderJsonOutput(array $outdated): string {
        return json_encode(['outdated' => $outdated], JSON_PRETTY_PRINT);
    }

    private function renderXmlOutput(array $outdated): string {
        $dom = new DOMDocument();
        $dom->loadXML('<?xml version="1.0" encoding="UTF-8" ?><outdated xmlns="https://phar.io/outdated" />');

        $root = $dom->documentElement;

        foreach ($outdated as $entry) {
            $node = $dom->createElementNS('https://phar.io/outdated', 'phar');

            foreach ($entry as $field => $value) {
                $node->setAttribute($field, $value);
            }
            $root->appendChild($node);
        }

        $dom->formatOutput       = true;
        $dom->preserveWhiteSpace = false;

        return $dom->saveXML();
    }

    private function writeToFile(string $output): void {
        $destination = $this->outdatedConfig->outputFilename();
        $destination->getDirectory()->ensureExists();
        file_put_contents($destination->asString(), $output . "\n");
    }
}
