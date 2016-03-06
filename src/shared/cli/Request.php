<?php
namespace PharIo\Phive\Cli;

class Request {

    private $parsed = false;

    /**
     * @var string[]
     */
    private $argv;

    /**
     * @var Options
     */
    private $phiveOptions;

    /**
     * @var string
     */
    private $command;

    /**
     * @var Options
     */
    private $options;

    /**
     * @param array $argv
     */
    public function __construct(array $argv) {
        $this->argv = $argv;
    }

    public function getCommand() {
        $this->parse();
        return $this->command;
    }

    private function parse() {
        if ($this->parsed === true) {
            return;
        }
        $this->parsed = true;

        // no parameters given?
        if (count($this->argv) == 1) {
            $this->command = 'help';
            $this->options = new Options([]);
            return;
        }

        if (count($this->argv) >= 2) {
            array_shift($this->argv);
            $this->phiveOptions = new Options($this->extractOptions());
            $this->command = array_shift($this->argv);
            $this->options = new Options($this->argv);
            return;
        }

        $this->options = new Options([]);
    }

    /**
     * @return string[]
     */
    private function extractOptions() {
        $opts = [];
        while (count($this->argv) && strpos($this->argv[0][0], '-') === 0) {
            $opts[] = array_shift($this->argv);
        }
        return $opts;
    }

    /**
     * @return Options
     */
    public function getCommandOptions() {
        $this->parse();
        return $this->options;
    }

    /**
     * @return Options
     */
    public function getPhiveOptions() {
        $this->parse();
        return $this->phiveOptions;
    }

}
