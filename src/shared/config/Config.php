<?php declare(strict_types = 1);
namespace PharIo\Phive;

use PharIo\FileSystem\Directory;
use PharIo\FileSystem\Filename;
use PharIo\Phive\Cli\Options;

class Config {

    /** @var Environment */
    private $environment;

    /** @var Options */
    private $cliOptions;

    /** @var \DateTimeImmutable */
    private $now;

    public function __construct(
        Environment $environment,
        Options $cliOptions,
        \DateTimeImmutable $now = null
    ) {
        $this->environment = $environment;
        $this->cliOptions  = $cliOptions;

        if ($now === null) {
            $now = new \DateTimeImmutable();
        }
        $this->now = $now;
    }

    public function getHomeDirectory(): Directory {
        if ($this->cliOptions->hasOption('home')) {
            return new Directory($this->cliOptions->getOption('home'));
        }

        return $this->environment->getHomeDirectory()->child('.phive');
    }

    public function getWorkingDirectory(): Directory {
        return $this->environment->getWorkingDirectory();
    }

    public function getToolsDirectory(): Directory {
        return new Directory('tools');
    }

    /**
     * @throws NoGPGBinaryFoundException
     */
    public function getGPGBinaryPath(): Filename {
        try {
            return $this->environment->getPathToCommand('gpg');
        } catch (EnvironmentException $e) {
            $message = "No executable gpg binary found. \n Either install gpg or enable the gnupg extension in PHP.";

            throw new NoGPGBinaryFoundException($message);
        }
    }

    public function getSourcesListUrl(): Url {
        return new Url('https://phar.io/data/repositories.xml');
    }

    public function getTrustedKeyIds(): KeyIdCollection {
        $idList = new KeyIdCollection();

        if ($this->cliOptions->hasOption('trust-gpg-keys')) {
            foreach (\explode(',', $this->cliOptions->getOption('trust-gpg-keys')) as $id) {
                $id = trim($id);
                $idList->addKeyId(
                    strpos($id, '0x') === 0 ? substr($id, 2) : $id
                );
            }
        }

        return $idList;
    }

    public function getMaxAgeForSourcesList(): \DateTimeImmutable {
        return $this->now->sub(new \DateInterval('P7D'));
    }
}
