<?php
namespace PharIo\Phive;

class KeyService {

    /**
     * @var KeyDownloader
     */
    private $keyDownloader;

    /**
     * @var KeyImporter
     */
    private $keyImporter;

    /**
     * @var Cli\Output
     */
    private $output;

    /**
     * @var Cli\Input
     */
    private $input;

    /**
     * @param KeyDownloader $keyDownloader
     * @param KeyImporter   $keyImporter
     * @param Cli\Output    $output
     * @param Cli\Input     $input
     */
    public function __construct(
        KeyDownloader $keyDownloader,
        KeyImporter $keyImporter,
        Cli\Output $output,
        Cli\Input $input
    ) {
        $this->keyDownloader = $keyDownloader;
        $this->keyImporter = $keyImporter;
        $this->output = $output;
        $this->input = $input;
    }

    /**
     * @param $keyId
     *
     * @return mixed
     * @throws VerificationFailedException
     */
    public function importKey($keyId) {
        $key = $this->downloadKey($keyId);

        $this->output->writeText("\n" . $key->getInfo() . "\n\n");
        if (!$this->input->confirm('Import this key?', false)) {
            throw new VerificationFailedException(sprintf('User declined import of key %s', $key->getId()));
        }

        return $this->keyImporter->importKey($key->getKeyData());
    }

    /**
     * @param $keyId
     *
     * @return PublicKey
     */
    private function downloadKey($keyId) {
        $this->output->writeInfo(sprintf('Downloading key %s', $keyId));

        return $this->keyDownloader->download($keyId);
    }

}
