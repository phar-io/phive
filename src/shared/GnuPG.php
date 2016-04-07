<?php
namespace PharIo\Phive;

/**
 * Class GnuPG
 *
 * This is a (thin) wrapper around the gnupg binary, mimicking the pecl/gnupg api
 * Currently, only the two methods required by phive (import and verify) are implemented
 *
 */
class GnuPG {

    /**
     * @var string
     */
    private $executable;

    /**
     * @var Directory
     */
    private $homeDirectory;

    /**
     * @var array
     */
    private $pipeDefinitions = [];

    /**
     * @var resource[]
     */
    private $pipeHandles = [];

    /**
     * @var resource
     */
    private $proc;

    /**
     * GnuPG constructor.
     *
     * @param string    $executable
     * @param Directory $homeDirectory
     */
    public function __construct($executable, Directory $homeDirectory) {
        $this->executable = $executable;
        $this->homeDirectory = $homeDirectory;
    }

    public function import($key) {
        $this->open(['--import']);
        $this->writeToPipe(0, $key);
        $status = $this->readFromStatus();
        $this->close();
        if (preg_match('=.*IMPORT_OK\s(\d+)\s(.*)=', $status, $matches)) {
            return [
                'imported'    => (int)$matches[1],
                'fingerprint' => $matches[2]
            ];
        }
        return ['imported' => 0];
    }

    private function open(array $params, array $pipes = []) {
        $this->proc = proc_open(
            $this->buildCLICommand($params),
            $this->buildPipes($pipes),
            $this->pipeHandles
        );
    }

    private function buildCLICommand(array $params) {
        return join(' ', array_merge([
            $this->executable,
            '--homedir ' . $this->homeDirectory,
            '--status-fd 3',
            '--no-tty',
            '--lock-multiple',
            '--no-permission-warning',
        ], $params));
    }

    /**
     * @param $pipes
     *
     * @return array
     */
    private function buildPipes(array $pipes) {
        $this->pipeDefinitions = array_merge(
            [
                0 => ['pipe', 'r'], // STDIN (SIGNATURE)
                1 => ['pipe', 'w'], // STDOUT
                2 => ['pipe', 'w'], // STDERR
                3 => ['pipe', 'w'], // FD-STATUS
            ],
            $pipes
        );
        return $this->pipeDefinitions;
    }

    private function writeToPipe($pipe, $content) {
        fwrite($this->pipeHandles[$pipe], $content);
        fflush($this->pipeHandles[$pipe]);
        fclose($this->pipeHandles[$pipe]);
        $this->pipeHandles[$pipe] = false;
    }

    private function readFromStatus() {
        stream_set_blocking($this->pipeHandles[3], 0);
        stream_set_read_buffer($this->pipeHandles[3], 0);
        $status = '';
        while (!feof($this->pipeHandles[3])) {
            $status .= fread($this->pipeHandles[3], 1);
        }
        return $status;
    }

    /**
     * @return int
     */
    private function close() {
        foreach ($this->pipeHandles as $id => $pipe) {
            if (is_resource($pipe)) {
                fclose($pipe);
                $this->pipeHandles[$id] = false;
            }
        }
        return proc_close($this->proc);
    }

    public function verify($message, $signature) {
        $params = [
            '--enable-special-filenames',
            '--verify',
            '-',
            "'-&4'"
        ];
        $this->open($params, [['pipe', 'r']]);
        $this->writeToPipe(0, $signature);
        $this->writeToPipe(4, $message);
        $status = $this->readFromStatus();
        $this->close();
        return $this->parseVerifyOutput($status);
    }

    /**
     * @param $status
     *
     * @return array|false
     */
    private function parseVerifyOutput($status) {
        $fingerprint = '';
        $timestamp = 0;
        $summary = false;
        foreach (explode("\n", $status) as $line) {
            $parts = explode(' ', $line);
            $fingerprint = $parts[2];

            if (strpos($line, 'VALIDSIG') !== false) {
                // [GNUPG:] VALIDSIG D8406D0D82947747{...}A394072C20A 2014-07-19 1405769272 0 4 0 1 10 00 D8{...}C20A
                /*
                VALIDSIG <args>

                The args are:

                - <fingerprint_in_hex>
                - <sig_creation_date>
                - <sig-timestamp>
                - <expire-timestamp>
                - <sig-version>
                - <reserved>
                - <pubkey-algo>
                - <hash-algo>
                - <sig-class>
                - [ <primary-key-fpr> ]
                */
                $timestamp = $parts[4];
                $summary = 0;
                break;
            }

            if (strpos($line, 'BADSIG') !== false) {
                // [GNUPG:] BADSIG 4AA394086372C20A Sebastian Bergmann <sb@sebastian-bergmann.de>
                $summary = 4;
                break;
            }

            if (strpos($line, 'ERRSIG') !== false) {
                // [GNUPG:] ERRSIG 4AA394086372C20A 1 10 00 1405769272 9
                // ERRSIG  <keyid>  <pkalgo> <hashalgo> <sig_class> <time> <rc>
                $timestamp = $parts[6];
                $summary = 128;
                break;
            }

        }

        if ($summary === false) {
            return false;
        }

        return [[
            'fingerprint' => $fingerprint,
            'validity'    => 0,
            'timestamp'   => $timestamp,
            'status'      => $status,
            'summary'     => $summary
        ]];
    }

}
