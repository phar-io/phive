<?php
namespace PharIo\Phive\Cli;

class ConsoleInput implements Input {

    /**
     * @var Output
     */
    private $output;

    /**
     * @var resource
     */
    private $inputStream;

    /**
     * @param Output $output
     * @param $inputStreamHandle
     */
    public function __construct(Output $output, $inputStreamHandle = STDIN) {
        $this->output = $output;
        $this->inputStream = $inputStreamHandle;
    }

    /**
     * @param string $message
     * @param bool $default
     *
     * @return bool
     */
    public function confirm($message, $default = true) {
        $yesOption = $default === true ? 'Y' : 'y';
        $noOption = $default === false ? 'N' : 'n';
        do {
            $this->output->writeText(rtrim($message) . sprintf(' [%s|%s] ', $yesOption, $noOption));
            $response = strtolower(rtrim(fgets($this->inputStream)));
        } while (!in_array($response, ['y', 'n', '']));

        if ($response === '') {
            return $default;
        }

        return ($response === 'y');
    }
}
