<?php
namespace PharIo\Phive\IntegrationTests;

use PharIo\Phive\Directory;

class GnuPGTest extends IntegrationTestCase {

    public function testVerifyWithValidSignature() {

        $gnupg = $this->getFactory()->getShellBasedGnupg(new Directory(__DIR__ . '/fixtures/.gpg'));
        $message = file_get_contents(__DIR__ . '/fixtures/message.txt');
        $signature = file_get_contents(__DIR__ . '/fixtures/message.txt.asc');

        $actual = $gnupg->verify($message, $signature);
        $this->assertSame(0, $actual[0]['summary']);
    }

    public function testVerifyWithInvalidSignature() {
        $gnupg = $this->getFactory()->getShellBasedGnupg(new Directory(__DIR__ . '/fixtures/.gpg'));

        $message = 'foobar';
        $signature = file_get_contents(__DIR__ . '/fixtures/message.txt.asc');

        $actual = $gnupg->verify($message, $signature);
        $this->assertSame(4, $actual[0]['summary']);
    }

    public function testVerifyWithUnknownKey() {
        $gnupg = $this->getFactory()->getShellBasedGnupg(new Directory(__DIR__ . '/fixtures/.gpg'));

        $message = file_get_contents(__DIR__ . '/fixtures/message.txt');
        $signature = file_get_contents(__DIR__ . '/fixtures/message.txt.unknown-key.asc');

        $actual = $gnupg->verify($message, $signature);
        $this->assertSame(128, $actual[0]['summary']);
    }

}
