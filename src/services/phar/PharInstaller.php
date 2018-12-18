<?php
namespace PharIo\Phive;

use PharIo\FileSystem\File;
use PharIo\FileSystem\Filename;

abstract class PharInstaller {

    /**
     * @var Cli\Output
     */
    private $output;

    /**
     * @param Cli\Output $output
     */
    public function __construct(Cli\Output $output) {
        $this->output = $output;
    }

    /**
     * @param File     $phar
     * @param Filename $destination
     * @param bool     $copy
     */
    public function install(File $phar, Filename $destination, $copy) {
        $this->ensureDestinationIsWritable($destination);

        if ($destination->exists()) {
            unlink($destination->asString());
        }

        if ($copy) {
            $this->copy($phar->getFilename(), $destination);
            return;
        }
        $this->link($phar->getFilename(), $destination);
    }

    /**
     * @return Cli\Output
     */
    protected function getOutput() {
        return $this->output;
    }

    /**
     * @param Filename $phar
     * @param Filename $destination
     */
    protected function copy(Filename $phar, Filename $destination) {
        $this->getOutput()->writeInfo(
            sprintf('Copying %s to %s', basename($phar->asString()), $destination->asString())
        );
        copy($phar->asString(), $destination->asString());
        chmod($destination, 0755);
    }

    /**
     * @param Filename $phar
     * @param Filename $destination
     *
     * @throws LinkCreationFailedException
     */
    abstract protected function link(Filename $phar, Filename $destination);

    /**
     * @param Filename $destination
     *
     * @throws FileNotWritableException
     */
    private function ensureDestinationIsWritable(Filename $destination) {
        if (!$destination->isWritable()) {
            throw new FileNotWritableException(sprintf('File %s is not writable.', $destination->asString()));
        }
    }

}
