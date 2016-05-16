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
    * @var Environment
    */
    private $environment;

    /**
     * @param Directory $pharDirectory
     * @param Cli\Output $output
     * @param Environment $environment
     */
    public function __construct(Directory $pharDirectory, Cli\Output $output, Environment $environment) {
        $this->pharDirectory = $pharDirectory;
        $this->output = $output;
        $this->environment = $environment;
    }

    /**
     * @param File   $phar
     * @param string $destination
     * @param bool   $copy
     */
    public function install(File $phar, $destination, $copy) {
        if (file_exists($destination)) {
            unlink($destination);
        }

        if (!$copy && $this->environment instanceof WindowsEnvironment && !$this->environment->hasAdminPrivileges()) {
            $this->output->writeWarning(
                'Windows allows creation of symlinks only when having Administrator privileges.' . "\n" .
                '          Creating a copy of the PHAR instead.'
            );
            $copy = true;
        }

        if ($copy) {
            $this->copy($phar, $destination);
            return;
        }
        $this->link($phar, $destination);
    }

    /**
     * @param File   $phar
     * @param string $destination
     */
    private function copy(File $phar, $destination) {
        $this->output->writeInfo(sprintf('Copying %s to %s', $phar->getFilename(), $destination));
        copy($this->pharDirectory . DIRECTORY_SEPARATOR . $phar->getFilename(), $destination);
        chmod($destination, 0755);
    }

    /**
     * @param File   $phar
     * @param string $destination
     */
    private function link(File $phar, $destination) {
        $this->output->writeInfo(sprintf('Symlinking %s to %s', $phar->getFilename(), $destination));
        symlink($this->pharDirectory . DIRECTORY_SEPARATOR . $phar->getFilename(), $destination);
    }

}
