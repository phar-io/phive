<?php
namespace PharIo\Phive;

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
     */
    public function activate(Filename $pharLocation, Filename $linkDestination) {
        $template = str_replace(self::PHAR_PLACEHOLDER, $pharLocation->asString(), $this->template);
        $linkFilename = new Filename($linkDestination->asString() . '.bat');
        file_put_contents($linkFilename, $template);

        return $linkFilename;
    }

}
