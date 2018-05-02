<?php

namespace PharIo\Phive;

use PharIo\FileSystem\Filename;

/**
 * The WindowsPharActivator tries to set a hardlink for the phar file.
 * If that works it will add a bat file for the link, for
 * the original phar file, otherwise.
 */
class WindowsPharActivator implements PharActivator {

    const PHAR_PLACEHOLDER = '##PHAR_FILENAME##';

    private $template;

    /**
     * @param string $template
     */
    public function __construct($template) {
        $this->template = $template;
    }

    /**
     * @param Filename $pharLocation
     * @param Filename $linkDestination
     *
     * @return Filename
     */
    public function activate(Filename $pharLocation, Filename $linkDestination) {
        $this->ensureDestinationIsWritable($linkDestination);

        $destination = new Filename($linkDestination->asString().'.phar');
        if ($this->addFileLink($pharLocation, $destination)) {
            $template = str_replace(self::PHAR_PLACEHOLDER, '%~dp0'.basename($destination), $this->template);
        } else {
            $template = str_replace(self::PHAR_PLACEHOLDER, $pharLocation->withAbsolutePath(), $this->template);
        }
        $linkFilename = new Filename($linkDestination->asString().'.bat');
        file_put_contents($linkFilename, $template);
        return $destination;
    }

    /**
     * Set a windows hardlink
     *
     * @param Filename $source
     * @param Filename $target
     * @return bool
     */
    private function addFileLink(Filename $source, Filename $target) {
        $output = [];
        $returnValue = 0;
        if (file_exists($target)) {
            unlink($target);
        }
        $command = 'mklink /h '.self::escapeShellFileArg($target).' '.self::escapeShellFileArg($source);
        exec($command, $output, $returnValue);
        return $returnValue === 0 && file_exists($target);
    }

    /**
     * Ensure the OS specific directory separator and escape the filename for use as a argument in a shell command.
     *
     * @param Filename $filename
     * @return string
     */
    private static function escapeShellFileArg(Filename $filename) {
        return escapeshellarg(str_replace('/', DIRECTORY_SEPARATOR, $filename->withAbsolutePath()));
    }

    /**
     * @param Filename $destination
     *
     * @throws FileNotWritableException
     */
    private function ensureDestinationIsWritable(Filename $destination) {
        if (!$destination->getDirectory()->isWritable()) {
            throw new FileNotWritableException(sprintf('File %s is not writable.', $destination->asString()));
        }
    }
}
