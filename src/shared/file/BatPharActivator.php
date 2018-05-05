<?php
namespace PharIo\Phive;

use PharIo\FileSystem\Filename;

class BatPharActivator implements PharActivator {

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
     * @throws FileNotWritableException
     */
    public function activate(Filename $pharLocation, Filename $linkDestination) {
        $linkFilename = new Filename($linkDestination->asString() . '.bat');
        if (!$linkDestination->getDirectory()->isWritable()) {
            throw new FileNotWritableException(sprintf('File %s is not writable.', $linkFilename->asString()));
        }
        if ((string)$pharLocation->getDirectory() === (string)$linkFilename->getDirectory()) {
            $pathToPhar = '%~dp0' . $pharLocation->getRelativePathTo($linkFilename->getDirectory())->asString();
        } else {
            $pathToPhar = $pharLocation->asString();
        }
        $template = str_replace(self::PHAR_PLACEHOLDER, $pathToPhar, $this->template);
        file_put_contents($linkFilename, $template);

        return $linkFilename;
    }

}
