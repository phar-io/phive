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
     * @var Filename
     */
    private $executable;

    /**
     * @var Directory
     */
    private $homeDirectory;

    /**
     * @var PipeIO
     */
    private $pipeIO;

    /**
     * @param Filename $executable
     * @param Directory $homeDirectory
     * @param PipeIO $pipeIO
     */
    public function __construct(Filename $executable, Directory $homeDirectory, PipeIO $pipeIO) {
        $this->executable = $executable;
        $this->homeDirectory = $homeDirectory;
        $this->pipeIO = $pipeIO;
    }

    /**
     * @param string $key
     *
     * @return array
     */
    public function import($key) {
        $params = array_merge(
            $this->getDefaultGpgParams(),
            ['--import']
        );        
        $this->pipeIO->open($this->executable, $params);
        $this->pipeIO->writeToPipe(PipeIO::PIPE_STDIN, $key);
        $status = $this->pipeIO->readFromPipe(PipeIO::PIPE_FD_STATUS);
        $this->pipeIO->close();
        if (preg_match('=.*IMPORT_OK\s(\d+)\s(.*)=', $status, $matches)) {
            return [
                'imported'    => (int)$matches[1],
                'fingerprint' => $matches[2]
            ];
        }
        return ['imported' => 0];
    }

    /**
     * @param string $message
     * @param string $signature
     *
     * @return array|false
     */
    public function verify($message, $signature) {
        $params = array_merge(
            $this->getDefaultGpgParams(),
            [
                '--enable-special-filenames',
                '--verify',
                '-',
                "'-&4'"
            ]
        );
        $this->pipeIO->open($this->executable, $params, [4 => ['pipe', 'r']]);
        $this->pipeIO->writeToPipe(PipeIO::PIPE_STDIN, $signature);
        $this->pipeIO->writeToPipe(4, $message);
        $status = $this->pipeIO->readFromPipe(PipeIO::PIPE_FD_STATUS);
        $this->pipeIO->close();
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
            if (count($parts) < 3) {
                continue;
            }
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

    /**
     * @return array
     */
    private function getDefaultGpgParams() {
        return [
            '--homedir ' . $this->homeDirectory,
            '--status-fd ' . PipeIO::PIPE_FD_STATUS,
            '--no-tty',
            '--lock-multiple',
            '--no-permission-warning',
        ];
    }

}
