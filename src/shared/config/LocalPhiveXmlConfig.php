<?php declare(strict_types = 1);
namespace PharIo\Phive;

use PharIo\FileSystem\Filename;
use PharIo\Version\VersionConstraintParser;

class LocalPhiveXmlConfig extends PhiveXmlConfig {
    /** @var Environment */
    private $environment;

    public function __construct(XmlFile $configFile, VersionConstraintParser $versionConstraintParser, Environment $environment) {
        parent::__construct($configFile, $versionConstraintParser);
        $this->environment = $environment;
    }

    protected function getLocation(InstalledPhar $installedPhar): Filename {
        return $installedPhar->getLocation()->getRelativePathTo($this->environment->getWorkingDirectory());
    }
}
