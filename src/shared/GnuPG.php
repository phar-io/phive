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
     * @var Directory
     */
    private $tmpDirectory;

    /**
     * @param Filename  $executable
     * @param Directory $tmpDirectory
     * @param Directory $homeDirectory
     */
    public function __construct(Filename $executable, Directory $tmpDirectory, Directory $homeDirectory) {
        $this->executable = $executable;
        $this->tmpDirectory = $tmpDirectory;
        $this->homeDirectory = $homeDirectory;
    }

    /**
     * @param string $key
     *
     * @return array
     */
    public function import($key) {
        $tmpFile = $this->createTemporaryFile($key);
        $result = $this->execute([
            '--import',
            escapeshellarg($tmpFile->asString())
        ]);
        unlink($tmpFile);

        if (preg_match('=.*IMPORT_OK\s(\d+)\s(.*)=', join('', $result), $matches)) {
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
        $messageFile = $this->createTemporaryFile($message);
        $signatureFile = $this->createTemporaryFile($signature);

        $result = $this->execute([
            '--verify',
            escapeshellarg($signatureFile->asString()),
            escapeshellarg($messageFile->asString())
        ]);

        unlink($signatureFile);
        unlink($messageFile);

        return $this->parseVerifyOutput($result);
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
        foreach ($status as $line) {
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
     * @return string[]
     */
    private function getDefaultGpgParams() {
        return [
            '--homedir ' . escapeshellarg($this->homeDirectory),
            '--quiet',
            '--status-fd 1',
            '--lock-multiple',
            '--no-permission-warning',
            '--no-greeting',
            '--exit-on-status-write-error',
            '--batch',
            '--no-tty'
        ];
    }

    /**
     * @param string[] $params
     *
     * @return mixed
     */
    private function execute(array $params) {
        $devNull = stripos(PHP_OS, 'win') === 0 ? 'nul' : '/dev/null';

        $command = sprintf('%s %s %s 2>%s',
            escapeshellarg($this->executable),
            join(' ', $this->getDefaultGpgParams()),
            join(' ', $params),
            $devNull
        );
        exec($command, $result, $rc);
        return $result;
    }

    /**
     * @param string $content
     *
     * @return Filename
     */
    private function createTemporaryFile($content) {
        $tmpFile = $this->tmpDirectory->file(uniqid('phive_gpg_', true));
        file_put_contents($tmpFile->asString(), $content);
        return $tmpFile;
    }

}
