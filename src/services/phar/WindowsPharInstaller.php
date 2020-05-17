<?php declare(strict_types = 1);
namespace PharIo\Phive;

use PharIo\FileSystem\Filename;

class WindowsPharInstaller extends PharInstaller {
    public const PHAR_PLACEHOLDER = '##PHAR_FILENAME##';

    /** @var string */
    private $template;

    public function __construct(Cli\Output $output, string $template) {
        parent::__construct($output);
        $this->template = $template;
    }

    protected function copy(Filename $phar, Filename $destination): void {
        parent::copy($phar, $destination);
        $this->link($destination, $destination);
    }

    protected function link(Filename $phar, Filename $destination): void {
        $linkFilename = new Filename($destination->withoutExtension()->asString() . '.bat');

        if ($phar->getDirectory()->asString() === $linkFilename->getDirectory()->asString()) {
            $pathToPhar = '%~dp0' . $phar->getRelativePathTo($linkFilename->getDirectory())->asString();
        } else {
            $pathToPhar = $phar->asString();
        }
        $template = \str_replace(self::PHAR_PLACEHOLDER, $pathToPhar, $this->template);
        $this->getOutput()->writeInfo(
            \sprintf('Linking %s to %s', $phar->asString(), $linkFilename->asString())
        );

        $linkFilename->getDirectory()->ensureExists();
        \file_put_contents($linkFilename->asString(), $template);
    }
}
