<?php
namespace TheSeer\Phive {

    class CliGnupgKeyImporter implements KeyImporter {

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
         * @param string $key
         *
         * @return KeyImportResult|void
         */
        public function importKey($key) {
            $proc = proc_open(
                $this->buildCliCommand(),
                [
                    ['pipe', 'r'], // STDIN
                    ['pipe', 'w'], // STDOUT
                    ['pipe', 'w'], // STDERR
                    ['pipe', 'w']  // FD-STATUS
                ],
                $pipes
            );

            fwrite($pipes[0], $key);
            fflush($pipes[0]);
            fclose($pipes[0]);

            stream_set_blocking($pipes[3], 0);
            stream_set_read_buffer($pipes[3], 0);
            $status = '';
            while (!feof ($pipes[3])) {
                $status .= fread($pipes[3], 1);
            }

            fclose($pipes[1]);
            fclose($pipes[2]);
            fclose($pipes[3]);
            proc_close($proc);

            if (preg_match('=.*IMPORT_OK\s(\d+)\s(.*)=', $status, $matches)) {
                return new KeyImportResult( (int)$matches[1], $matches[2]);
            }
            return new KeyImportResult(0);
        }

        private function buildCLICommand() {
            return join(' ', [
                $this->gnupgBinary,
                '--status-fd 3',
                '--no-tty',
                '--lock-multiple',
                '--no-permission-warning',
                '--import']
            );
        }

    }

}

