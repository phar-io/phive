<?php
namespace PharIo\Phive\Cli;

class ConsoleInput implements Input {

    /**
     * @var Output
     */
    private $output;

    /**
     * ConsoleInput constructor.
     *
     * @param Output $output
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
        do {
            $this->output->writeText(rtrim($message) . ' [Y|n] ');
            $response = strtolower(rtrim(fgets(STDIN)));
        } while (!in_array($response, ['y', 'n']));

        return ($response === 'y');
    }
}
