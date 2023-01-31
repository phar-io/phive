<?php declare(strict_types=1);
/*
 * This file is part of Phive.
 *
 * Copyright (c) Arne Blankerts <arne@blankerts.de>, Sebastian Heuer <sebastian@phpeople.de> and contributors
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */
namespace PharIo\Phive;

use function in_array;
use function sprintf;

class KeyService {
    /** @var KeyDownloader */
    private $keyDownloader;

    /** @var KeyImporter */
    private $keyImporter;

    /** @var Cli\Output */
    private $output;

    /** @var Cli\Input */
    private $input;

    /** @var TrustedCollection */
    private $trusted;

    public function __construct(
        KeyDownloader $keyDownloader,
        KeyImporter $keyImporter,
        TrustedCollection $trusted,
        Cli\Output $output,
        Cli\Input $input
    ) {
        $this->keyDownloader = $keyDownloader;
        $this->keyImporter   = $keyImporter;
        $this->output        = $output;
        $this->input         = $input;
        $this->trusted       = $trusted;
    }

    public function importKey(string $keyId, array $knownFingerprints): KeyImportResult {
        $key = $this->downloadKey($keyId);

        if (!empty($knownFingerprints) && !in_array($key->getFingerprint(), $knownFingerprints, true)) {
            $this->output->writeWarning(
                "This is NOT a key that has been used to install previous versions of this PHAR.\n"
                . "           While this can be perfectly valid (maybe the maintainer switched to a new key),\n"
                . '           please make sure this key belongs to the maintainer of the PHAR you are going to install.'
            );
        }

        $this->output->writeText("\n" . $key->getInfo() . "\n\n");

        if (!$this->allowedToImport($key)) {
            $this->output->writeError(sprintf('User declined import of key %s', $key->getId()));

            return new KeyImportResult(0);
        }

        return $this->keyImporter->importKey($key->getKeyData());
    }

    private function downloadKey(string $keyId): PublicKey {
        $this->output->writeInfo(sprintf('Downloading key %s', $keyId));

        return $this->keyDownloader->download($keyId);
    }

    private function allowedToImport(PublicKey $key): bool {
        return $this->trusted->has($key) || $this->input->confirm('Import this key?', false);
    }
}
