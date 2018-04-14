<?php
namespace PharIo\Phive;

use PharIo\Phive\Cli\ConsoleTable;

class StatusCommand implements Cli\Command {

    /**
     * @var PhiveXmlConfig
     */
    private $phiveXmlConfig;

    /**
     * @var Cli\Output
     */
    private $output;

    /**
     * @var PharRegistry
     */
    private $pharRegistry;

    /**
     * @param PhiveXmlConfig $phiveXmlConfig
     * @param Cli\Output     $output
     */
    public function __construct(PhiveXmlConfig $phiveXmlConfig, PharRegistry $pharRegistry, Cli\Output $output) {
        $this->phiveXmlConfig = $phiveXmlConfig;
        $this->pharRegistry = $pharRegistry;
        $this->output = $output;
    }

    public function execute() {

        $this->output->writeText('PHARs configured in phive.xml:' . "\n\n");

        $table = new ConsoleTable(['Alias/URL', 'Version Constraint', 'Installed', 'Location', 'Key Ids']);

        foreach ($this->phiveXmlConfig->getPhars() as $phar) {
            $installed = '-';
            if ($phar->isInstalled()) {
                $installed = $phar->getInstalledVersion()->getVersionString();
            }
            $location = $phar->hasLocation() ? $phar->getLocation()->asString() : '-';
            $keys = implode(
                ', ',
                array_map(
                    function($key) {
                        return substr($key, -16);
                    },
                    $this->pharRegistry->getKnownSignatureFingerprints($phar->getName())
                )
            );
            $table->addRow(
                [$phar->getName(), $phar->getVersionConstraint()->asString(), $installed, $location, $keys]
            );
        }

        $this->output->writeText($table->asString());
    }

}
