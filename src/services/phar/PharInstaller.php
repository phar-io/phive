<?php declare(strict_types = 1);
namespace PharIo\Phive;

use PharIo\FileSystem\File;
use PharIo\FileSystem\Filename;

abstract class PharInstaller {
    /** @var Cli\Output */
    private $output;

    public function __construct(Cli\Output $output) {
        $this->output = $output;
    }

    /**
     * @param bool $copy
     */
    public function install(File $phar, Filename $destination, $copy): void {
        $this->ensureDestinationIsWritable($destination);

        if ($destination->exists()) {
            \unlink($destination->asString());
        }

        if ($copy) {
            $this->copy($phar->getFilename(), $destination);

            return;
        }
        $this->link($phar->getFilename(), $destination);
    }

    protected function getOutput(): Cli\Output {
        return $this->output;
    }

    protected function copy(Filename $phar, Filename $destination): void {
        $this->getOutput()->writeInfo(
            \sprintf('Copying %s to %s', \basename($phar->asString()), $destination->asString())
        );
        \copy($phar->asString(), $destination->asString());
        \chmod($destination->asString(), 0755);
    }

    /**
     * @throws LinkCreationFailedException
     */
    abstract protected function link(Filename $phar, Filename $destination): void;

    /**
     * @throws FileNotWritableException
     */
    private function ensureDestinationIsWritable(Filename $destination): void {
        if (!$destination->isWritable()) {
            throw new FileNotWritableException(\sprintf('File %s is not writable.', $destination->asString()));
        }
    }
}
