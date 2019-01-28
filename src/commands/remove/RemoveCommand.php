<?php declare(strict_types = 1);
namespace PharIo\Phive;

class RemoveCommand implements Cli\Command {

    /** @var RemoveCommandConfig */
    private $config;

    /** @var PharRegistry */
    private $pharRegistry;

    /** @var Cli\Output */
    private $output;

    /** @var PhiveXmlConfig */
    private $phiveXmlConfig;

    /**
     * @internal param PharService $pharService
     */
    public function __construct(
        RemoveCommandConfig $config,
        PharRegistry $pharRegistry,
        Cli\Output $output,
        PhiveXmlConfig $phiveXmlConfig
    ) {
        $this->config         = $config;
        $this->pharRegistry   = $pharRegistry;
        $this->output         = $output;
        $this->phiveXmlConfig = $phiveXmlConfig;
    }

    public function execute(): void {
        $name = $this->config->getPharName();

        if (!$this->phiveXmlConfig->hasPhar($name)) {
            throw new NotFoundException(\sprintf('PHAR %s not found in phive.xml, aborting.', $name));
        }
        $location = $this->phiveXmlConfig->getPharLocation($name)->withAbsolutePath();
        $phar     = $this->pharRegistry->getByUsage($location);
        $this->output->writeInfo(
            \sprintf('Removing Phar %s %s', $phar->getName(), $phar->getVersion()->getVersionString())
        );
        $this->phiveXmlConfig->removePhar($phar->getName());
        $this->pharRegistry->removeUsage($phar, $location);
        \unlink($location);

        if (!$this->pharRegistry->hasUsages($phar)) {
            $this->output->writeInfo(
                \sprintf(
                    'Phar %s %s has no more known usages. You can run \'phive purge\' to remove unused Phars.',
                    $phar->getName(),
                    $phar->getVersion()->getVersionString()
                )
            );
        }
    }
}
