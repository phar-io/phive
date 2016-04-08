<?php
namespace PharIo\Phive;

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
     * @param int    $mode
     */
    public function __construct($path, $mode = 0775) {
        $this->ensureModeIsInteger($mode);
        $this->ensureExists($path);
        $this->ensureIsDirecotry($path);
        $this->ensureMode($path, $mode);
        $this->path = $path;
        $this->mode = $mode;
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
            mkdir($path, 0777, true);
            clearstatcache(true, $path);
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
     * @param int    $mode
     *
     * @throws DirectoryException
     */
    private function ensureMode($path, $mode) {
        if (octdec(substr(sprintf('%o', fileperms($path)), -4)) === $mode) {
            return;
        }
        try {
            $rc = chmod($path, $mode);
            if (!$rc) {
                throw new \ErrorException('Chmod call returned false.');
            }
            clearstatcache(true, $path);
        } catch (\ErrorException $e) {
            throw new DirectoryException(
                sprintf('Setting mode for directory "%s" failed.', $path),
                DirectoryException::ChmodFailed,
                $e
            );
        }
    }

    /**
     * @param string $child
     * @param int    $mode
     *
     * @return Directory
     */
    public function child($child, $mode = null) {
        return new Directory(
            $this->path . DIRECTORY_SEPARATOR . $child,
            $mode !== null ? $mode : $this->mode
        );
    }

    /**
     * @return Directory
     */
    public function parent() {
        return new Directory(
            $this->path . DIRECTORY_SEPARATOR . '/../'
        );
    }

    /**
     * @param string $filename
     *
     * @return Filename
     */
    public function file($filename) {
        return new Filename($this->path . DIRECTORY_SEPARATOR . $filename);
    }

    /**
     * @return string
     */
    public function __toString() {
        return $this->path;
    }

    private function ensureIsDirecotry($path) {
        if (is_dir($path)) {
            return;
        }
        throw new DirectoryException(
            sprintf('Path %s exists but is not a directory', $path),
            DirectoryException::InvalidType
        );
    }
}
