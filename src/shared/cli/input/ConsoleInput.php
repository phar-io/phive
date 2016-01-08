<?php
namespace PharIo\Phive\Cli;

class ConsoleInput implements Input {

    /**
     * @var Cli\Output
     */
    private $output;

    /**
     * ConsoleInput constructor.
     *
     * @param Cli\Output $output
     */
    public function __construct(Output $output) {
        $this->output = $output;
    }

    /**
     * @param string $message
     *
     * @return bool
     */
    public function confirm($message) {
        $this->output->writeText(rtrim($message) . ' [Y|n] ');
        $response = fgetc(STDIN);
        return (trim($response) === '' || strpos('Yy', $response[0]) !== false);
    }
}
