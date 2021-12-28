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

use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * @covers \PharIo\Phive\GnupgKeyDownloader
 */
class GnupgKeyDownloaderTest extends TestCase {
    /** @var CurlHttpClient|ObjectProphecy */
    private $curl;

    /** @var Cli\Output|ObjectProphecy */
    private $output;

    protected function setUp(): void {
        $this->curl   = $this->prophesize(CurlHttpClient::class);
        $this->output = $this->prophesize(Cli\Output::class);
    }

    public function testReturnsParsedPublicKey(): void {
        $keyid = '12345678';

        $response = $this->prophesize(HttpResponse::class);
        $response->isSuccess()->willReturn(true);
        $response->getHttpCode()->willReturn(200);
        $response->getBody()->willReturn('KEYDATA');
        $response->isNotFound()->willReturn(false);

        $reader = $this->prophesize(PublicKeyReader::class);
        $reader->parse($keyid, 'KEYDATA')->shouldBeCalled()->willReturn(
            $this->prophesize(PublicKey::class)
        );

        $this->curl->get(
            new Url('https://example.com/pks/lookup?op=get&options=mr&search=0x' . $keyid)
        )->shouldBeCalled()->willReturn($response->reveal());

        $downloader = new GnupgKeyDownloader(
            $this->curl->reveal(),
            ['example.com'],
            $reader->reveal(),
            $this->output->reveal()
        );
        $downloader->download($keyid);
    }
}
