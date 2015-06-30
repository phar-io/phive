<?php
namespace TheSeer\Phive {

    /**
     * GPG Signature Verification using the GnuPG binary.
     */
    class CliGnupgSignatureVerifier implements SignatureVerifier {

        /**
         * @var string
         */
        private $gnupgBinary;

        /**
         * CliGnupgKeyImporter constructor.
         *
         * @param string   $gnupgBinary
         */
        public function __construct($gnupgBinary) {
            $this->gnupgBinary = $gnupgBinary;
        }

        /**
         * @param string $message
         * @param string $signature
         *
         * @return GnupgVerificationResult
         * @throws VerificationFailedException
         */
        public function verify($message, $signature) {
            $proc = proc_open(
                $this->buildCliCommand(),
                [
                    0 => ['pipe', 'r'], // STDIN (SIGNATURE)
                    1 => ['pipe', 'w'], // STDOUT
                    2 => ['pipe', 'w'], // STDERR
                    3 => ['pipe', 'w'], // FD-STATUS
                    4 => ['pipe', 'r']  // MESSAGE
                ],
                $pipes
            );
            fwrite($pipes[0], $signature);
            fflush($pipes[0]);
            fclose($pipes[0]);

            fwrite($pipes[4], $message . '....');
            fflush($pipes[4]);
            fclose($pipes[4]);

            stream_set_blocking($pipes[3], 0);
            stream_set_read_buffer($pipes[3], 0);
            $status = '';
            while (!feof ($pipes[3])) {
                $status .= fread($pipes[3], 1);
            }

            fclose($pipes[1]);
            fclose($pipes[2]);
            fclose($pipes[3]);
            $returnCode = proc_close($proc);

            return $this->parseStatus($returnCode, $status);
        }

        private function buildCLICommand() {
            return join(' ', [
                    $this->gnupgBinary,
                    '--status-fd 3',
                    '--no-tty',
                    //'--lock-multiple',
                    '--no-permission-warning',
                    '--enable-special-filenames',
                    '--verify',
                    '-',
                    "'-&4'"
                ]
            );
        }

        /**
         *
         * @param $returnCode
         * @param $status
         *
         * @return GnupgVerificationResult
         * @throws VerificationFailedException
         */
        private function parseStatus($returnCode, $status) {
            var_dump($returnCode, $status);
            if ($returnCode != 0 || strpos($status, 'BADSIG') !== FALSE) {
                throw new VerificationFailedException('FAILED.');
            }
            return new GnupgVerificationResult([]);
        }

    }

}

