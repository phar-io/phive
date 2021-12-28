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

use function sprintf;
use Throwable;

class GnupgKeyDownloader implements KeyDownloader {
    public const PATH = '/pks/lookup';

    /** @var HttpClient */
    private $httpClient;

    /** @var string[] */
    private $keyServers;

    /** @var Cli\Output */
    private $output;

    /** @var PublicKeyReader */
    private $reader;

    /**
     * @param string[] $keyServers
     */
    public function __construct(HttpClient $httpClient, array $keyServers, PublicKeyReader $reader, Cli\Output $output) {
        $this->httpClient = $httpClient;
        $this->keyServers = $keyServers;
        $this->output     = $output;
        $this->reader     = $reader;
    }

    /**
     * @throws DownloadFailedException
     */
    public function download(string $keyId): PublicKey {
        $publicParams = [
            'op'      => 'get',
            'options' => 'mr',
            'search'  => '0x' . $keyId
        ];

        foreach ($this->keyServers as $keyServerName) {
            try {
                $publicKey = $this->httpClient->get((new Url('https://' . $keyServerName . self::PATH))->withParams($publicParams));
                $this->ensureSuccess($publicKey);
            } catch (HttpException $e) {
                $this->output->writeWarning(
                    sprintf('Failed with error code %s: %s', $e->getCode(), $e->getMessage())
                );

                continue;
            }

            $this->output->writeInfo('Successfully downloaded key.');

            try {
                return $this->reader->parse($keyId, $publicKey->getBody());
            } catch (Throwable $t) {
                $this->output->writeWarning(
                    sprintf('Parsing key data failed with error code %s: %s', $t->getCode(), $t->getMessage())
                );
            }
        }

        throw new DownloadFailedException(sprintf('PublicKey %s not found on key servers', $keyId));
    }

    /**
     * @throws HttpException
     */
    private function ensureSuccess(HttpResponse $response): void {
        if ($response->isNotFound()) {
            throw new HttpException('Key not found on keyserver', $response->getHttpCode());
        }

        if (!$response->isSuccess()) {
            throw new HttpException('Server reported an error', $response->getHttpCode());
        }
    }
}
