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

    public function getRegistry(): Filename {
        return $this->getHomeDirectory()->file('registry.xml');
    }

    public function getGlobalAuth(): Filename {
        return $this->getHomeDirectory()->file('auth.xml');
    }

    public function getPharIoRepositories(): Filename {
        return $this->getHomeDirectory()->file('repositories.xml');
    }

    public function getLocalRepositories(): Filename {
        return $this->getHomeDirectory()->file('local.xml');
    }

    public function getPharsDirectory(): Directory {
        return $this->getHomeDirectory()->child('phars');
    }

    public function getGPGDirectory(): Directory {
        return $this->getHomeDirectory()->child('gpg');
    }

    public function getHttpCacheDirectory(): Directory {
        return $this->getHomeDirectory()->child('http-cache');
    }

    public function getTemporaryWorkingDirectory(): Directory {
        return $this->getHomeDirectory()->child('_tmp_wrk');
    }

    public function getGlobalInstallation(): Filename {
        return $this->getHomeDirectory()->file('global.xml');
    }

    public function getWorkingDirectory(): Directory {
        return $this->environment->getWorkingDirectory();
    }

    public function getProjectRepositories(): Filename {
        return $this->getWorkingDirectory()->child('.phive')->file('repositories.xml');
    }

    public function getProjectAuth(): Filename {
        return $this->getWorkingDirectory()->child('.phive')->file('auth.xml');
    }

    public function getProjectInstallation(): Filename {
        return $this->getWorkingDirectory()->child('.phive')->file('phars.xml');
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
                $id = \trim($id);
                $idList->addKeyId(
                    \strpos($id, '0x') === 0 ? \substr($id, 2) : $id
                );
            }
        }

        return $idList;
    }

    public function getMaxAgeForSourcesList(): \DateTimeImmutable {
        return $this->now->sub(new \DateInterval('P7D'));
    }
}
