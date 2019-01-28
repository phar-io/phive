<?php declare(strict_types = 1);
namespace PharIo\Phive;

use PharIo\FileSystem\Directory;

/**
 * @codeCoverageIgnore
 */
class Git {
    /** @var Directory; */
    private $workingDirectory;

    public function __construct(Directory $workingDirectory) {
        $this->workingDirectory = $workingDirectory;
    }

    public function isRepository(Directory $directory): bool {
        return \is_dir($directory . '/.git');
    }

    /**
     * @throws GitException
     */
    public function getMostRecentTag(Directory $directory): string {
        if (!$this->isRepository($directory)) {
            throw new GitException(\sprintf('%s is not a git repository', $directory));
        }
        \chdir($directory->__toString());
        $tag = @\exec('git describe --tags --always --dirty 2>' . $this->getDevNull(), $output, $returnCode);
        \chdir($this->workingDirectory->__toString());

        if ($returnCode !== 0) {
            throw new GitException('Could not determine most recent tag');
        }

        return $tag;
    }

    private function getDevNull(): string {
        return \strtolower(\substr(\PHP_OS, 0, 3)) === 'win' ? 'nul' : '/dev/null';
    }
}
