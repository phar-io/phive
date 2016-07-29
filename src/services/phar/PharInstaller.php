<?php
namespace PharIo\Phive;

class PharInstaller {

    /**
     * @var Directory
     */
    private $pharDirectory = '';

    /**
     * @var Cli\Output
     */
    private $output;

    /**
     * @var PharActivator
     */
    private $pharActivator;

    /**
     * @param Directory $pharDirectory
     * @param Cli\Output $output
     * @param PharActivator $pharActivator
     */
    public function __construct(
        Directory $pharDirectory,
        Cli\Output $output,
        PharActivator $pharActivator
    ) {
        $this->pharDirectory = $pharDirectory;
        $this->output = $output;
        $this->pharActivator = $pharActivator;
    }

    /**
     * @param File     $phar
     * @param Filename $destination
     * @param bool     $copy
     */
    public function install(File $phar, Filename $destination, $copy) {
        if ($destination->exists()) {
            unlink($destination->asString());
        }

        if ($copy) {
            $this->copy($phar, $destination);
            return;
        }
        $this->link($phar, $destination);
    }

    /**
     * @param File     $phar
     * @param Filename $destination
     */
    private function copy(File $phar, Filename $destination) {
        $this->output->writeInfo(sprintf('Copying %s to %s', $phar->getFilename(), $destination->asString()));
        copy($this->pharDirectory . DIRECTORY_SEPARATOR . $phar->getFilename(), $destination->asString());
        chmod($destination, 0755);
    }

    /**
     * @param File     $phar
     * @param Filename $destination
     */
    private function link(File $phar, Filename $destination) {
        $linkFilename = $this->pharActivator->activate($this->pharDirectory->file($phar->getFilename()), $destination);
        $this->output->writeInfo(sprintf('Linking %s to %s', $phar->getFilename(), $linkFilename->asString()));
    }

}
