<?php
namespace PharIo\Phive;

/**
 * @codeCoverageIgnore
 */
class Git {

    /**
     * @var Directory;
     */
    private $workingDirectory;

    /**
     * @param Directory $workingDirectory
     */
    public function __construct(Directory $workingDirectory) {
        $this->workingDirectory = $workingDirectory;
    }

    /**
     * @param Directory $directory
     *
     * @return bool
     */
    public function isRepository(Directory $directory) {
        return is_dir($directory . '/.git');
    }

    /**
     * @param Directory $directory
     *
     * @throws GitException
     *
     * @return string
     */
    public function getMostRecentTag(Directory $directory) {
        if (!$this->isRepository($directory)) {
            throw new GitException(sprintf('%s is not a git repository', $directory));
        }
        chdir($directory);
        $tag = @exec('git describe --tags --always --dirty 2>' . $this->getDevNull(), $output, $returnCode);
        chdir($this->workingDirectory);
        if ($returnCode !== 0) {
            throw new GitException('Could not determine most recent tag');
        }
        return $tag;
    }

    /**
     * @return string
     */
    private function getDevNull() {
        return strtolower(substr(PHP_OS, 0, 3)) == 'win' ? 'nul' : '/dev/null';
    }

}
