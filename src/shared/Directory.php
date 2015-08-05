<?php
namespace PharIo\Phive {

    class Directory {

        /**
         * @var string
         */
        private $path;

        /**
         * @var int
         */
        private $mode;

        /**
         * Directory constructor.
         *
         * @param string $path
         * @param int $mode
         */
        public function __construct($path, $mode = 0775) {
            $this->ensureModeIsInteger($mode);
            $this->ensureExists($path);
            $this->ensureMode($path, $mode);
            $this->path = $path;
            $this->mode = $mode;
        }

        /**
         * @param string $child
         * @param int $mode
         *
         * @return Directory
         */
        public function child($child, $mode = NULL) {
            return new Directory(
                $this->path . DIRECTORY_SEPARATOR . $child,
                $mode !== NULL ? $mode : $this->mode
            );
        }

        /**
         * @return string
         */
        public function __toString() {
            return $this->path;
        }

        /**
         * @param string $path
         *
         * @throws DirectoryException
         */
        private function ensureExists($path) {
            if (file_exists($path)) {
                return;
            }
            try {
                mkdir($path, 0777, TRUE);
            } catch (\ErrorException $e) {
                throw new DirectoryException(
                    sprintf('Creating directory "%s" failed.', $path),
                    DirectoryException::CreateFailed,
                    $e
                );
            }
        }

        /**
         * @param string $path
         * @param int $mode
         *
         * @throws DirectoryException
         */
        private function ensureMode($path, $mode) {
            if (fileperms($path) === $mode) {
                return;
            }
            try {
                chmod($path, $mode);
            } catch (\ErrorException $e) {
                throw new DirectoryException(
                    sprintf('Setting mode for directory "%s" failed.', $path),
                    DirectoryException::ChmodFailed,
                    $e
                );
            }
        }

        /**
         * @param $value
         *
         * @throws DirectoryException
         */
        private function ensureModeIsInteger($value) {
            if (is_int($value)) {
                return;
            }
            throw new DirectoryException(
                sprintf('Mode "%s" is not of integer type', $value),
                DirectoryException::InvalidMode
            );
        }
    }

}
