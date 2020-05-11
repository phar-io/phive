<?php declare(strict_types = 1);
namespace PharIo\Phive;

class MigrationFactory {

    /** @var Factory */
    private $factory;
    /** @var Environment */
    private $environment;
    /** @var Cli\Input */
    private $input;

    public function __construct(Factory $factory, Environment $environment, Cli\Input $input) {
        $this->factory     = $factory;
        $this->environment = $environment;
        $this->input       = $input;
    }

    /**
     * @return Migration[]
     */
    public function getMigrations(): array {
        return [
            new HomePharsXmlMigration($this->factory->getConfig(), $this->input),
            new HomePhiveXmlMigration($this->factory->getConfig(), $this->input),
            new ProjectPhiveXmlMigration($this->environment, $this->factory->getConfig(), $this->input),
        ];
    }
}
