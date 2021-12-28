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

use Exception;

/**
 * GPG Signature Verification using the GnuPG PECL Extension.
 */
class GnupgSignatureVerifier implements SignatureVerifier {
    /** @var GnuPG */
    private $gpg;

    /** @var KeyService */
    private $keyService;

    public function __construct(GnuPG $gpg, KeyService $keyService) {
        $this->gpg        = $gpg;
        $this->keyService = $keyService;
    }

    /**
     * @throws VerificationFailedException
     */
    public function verify(string $message, string $signature, array $knownFingerprints): VerificationResult {
        try {
            $result = $this->attemptVerification($message, $signature);

            if (!$result->isKnownKey()) {
                $importResult = $this->keyService->importKey($result->getFingerprint(), $knownFingerprints);

                if (!$importResult->isSuccess()) {
                    return $result;
                }

                return $this->attemptVerification($message, $signature);
            }

            return $result;
        } catch (Exception $e) {
            throw new VerificationFailedException($e->getMessage(), (int)$e->getCode(), $e);
        }
    }

    private function attemptVerification(string $message, string $signature): GnupgVerificationResult {
        $res = $this->gpg->verify($message, $signature);

        if (!$res) {
            throw new VerificationFailedException('GnuPG verify call returned false');
        }

        return new GnupgVerificationResult($res[0]);
    }
}
