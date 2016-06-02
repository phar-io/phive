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
     * @param string $path
     * @param int    $mode
     */
    public function __construct($path, $mode = 0775) {
        $this->ensureModeIsInteger($mode);
        $this->ensureExists($path);
        $this->ensureIsDirectory($path);
        $this->ensureMode($path, $mode);
        $this->path = $path;
        $this->mode = $mode;
    }

    /**
     * Taken from http://stackoverflow.com/questions/2637945/getting-relative-path-from-absolute-path-in-php#comment18071708_2637945
     * Credits go to http://stackoverflow.com/users/208809/gordon
     *
     * @param Directory $directory
     *
     * @return string
     */
    public function getRelativePathTo(Directory $directory) {
        $to = (string)$this;
        $from = (string)$directory;
        // some compatibility fixes for Windows paths
        $from = is_dir($from) ? rtrim($from, '\/') . '/' : $from;
        $to   = is_dir($to)   ? rtrim($to, '\/') . '/'   : $to;
        $from = str_replace('\\', '/', $from);
        $to   = str_replace('\\', '/', $to);

        $from     = explode('/', $from);
        $to       = explode('/', $to);
        $relPath  = $to;

        foreach($from as $depth => $dir) {
            // find first non-matching dir
            if($dir === $to[$depth]) {
                // ignore this directory
                array_shift($relPath);
            } else {
                // get number of remaining dirs to $from
                $remaining = count($from) - $depth;
                if($remaining > 1) {
                    // add traversals up to first matching dir
                    $padLength = (count($relPath) + $remaining - 1) * -1;
                    $relPath = array_pad($relPath, $padLength, '..');
                    break;
                } else {
                    $relPath[0] = './' . $relPath[0];
                }
            }
        }
        return implode('/', $relPath);
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

    /**
     * @param string $path
     *
     * @throws DirectoryException
     */
    private function ensureIsDirectory($path) {
        if (is_dir($path)) {
            return;
        }
        throw new DirectoryException(
            sprintf('Path %s exists but is not a directory', $path),
            DirectoryException::InvalidType
        );
    }
}
