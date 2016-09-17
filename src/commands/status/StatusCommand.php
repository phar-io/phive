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
     * @param PhiveXmlConfig $phiveXmlConfig
     * @param Cli\Output     $output
     */
    public function __construct(PhiveXmlConfig $phiveXmlConfig, Cli\Output $output) {
        $this->phiveXmlConfig = $phiveXmlConfig;
        $this->output = $output;
    }

    public function execute() {

        $this->output->writeText('PHARs configured in phive.xml:' . "\n\n");

        $table = new ConsoleTable(['Alias/URL', 'Version Constraint', 'Installed', 'Location']);

        foreach ($this->phiveXmlConfig->getPhars() as $phar) {
            $installed = '-';
            if ($phar->isInstalled()) {
                $installed = $phar->getInstalledVersion()->getVersionString();
            }
            $location = $phar->hasLocation() ? $phar->getLocation()->asString() : '-';
            $table->addRow(
                [$phar->getName(), $phar->getVersionConstraint()->asString(), $installed, $location]
            );

        }

        $this->output->writeText($table->asString());
    }

}
