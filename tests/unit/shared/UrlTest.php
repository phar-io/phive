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

use InvalidArgumentException;
use PharIo\FileSystem\Filename;
use PHPUnit\Framework\TestCase;

/**
 * @covers \PharIo\Phive\Url
 */
class UrlTest extends TestCase {
    /**
     * @dataProvider invalidUriProvider
     *
     * @param string $invalidUri
     */
    public function testThrowsExceptionIfProtocolIsNotHttps($invalidUri): void {
        $this->expectException(InvalidArgumentException::class);

        new Url($invalidUri);
    }

    public function invalidUriProvider() {
        return [
            ['http://example.com'],
            ['ftp://example.com'],
            ['example.com'],
            ['file:///example'],
            ['https://']
        ];
    }

    public function testReturnsTrueForFullyQualifiedURL(): void {
        $this->assertTrue(Url::isUrl('proto://host/path'));
    }

    public function testReturnsFalseForStringWithoutProtocolPart(): void {
        $this->assertFalse(Url::isUrl('host:path'));
    }

    public function testVerificationForHttpsReturnsFalseOnNonHttpsUrl(): void {
        $this->assertFalse(Url::isHttpsUrl('http://something'));
    }

    public function testVerificationForHttpsTrueFOnHttpsUrl(): void {
        $this->assertTrue(Url::isHttpsUrl('https://something'));
    }

    public function testCanBeCastToString(): void {
        $url = new Url('https://example.com');
        $this->assertSame('https://example.com', (string)$url);
    }

    public function testReturnsExpectedHostname(): void {
        $url = new Url('https://example.com/foo/bar');
        $this->assertSame('example.com', $url->getHostname());
    }

    /**
     * @param string $expected
     * @param string $base
     *
     * @dataProvider parameterPayload
     */
    public function testParametersGetAppliedCorrectly($expected, $base, array $params): void {
        $this->assertEquals(
            $expected,
            (string)(new Url($base))->withParams($params)
        );
    }

    public function parameterPayload() {
        return [
            [ // none
                'https://base/path',
                'https://base/path',
                []
            ], [ // one
                'https://base/?foo=1',
                'https://base/',
                ['foo' => 1]
            ], [ // multiple
                'https://base/path?foo=abc&bar=def',
                'https://base/path',
                ['foo' => 'abc', 'bar' => 'def']
            ], [ // add to existing
                'https://base/path?foo=abc&bar=def',
                'https://base/path?foo=abc',
                ['bar' => 'def']
            ], [ // space within
                'https://base/path/?foo=abc%20def',
                'https://base/path/',
                ['foo' => 'abc def']
            ], [ // special chars
                'https://base/path/?foo=%3F%26%3A-%20%2B%22%27%2F%5C',
                'https://base/path/',
                ['foo' => '?&:- +"\'/\\']
            ]
        ];
    }

    public function testPathCanBeRetrieved(): void {
        $this->assertEquals('/some', (new Url('https://host/some'))->getPath());
    }

    public function testReturnsRootPathForUrlsWithoutPath(): void {
        $this->assertEquals('/', (new Url('https://host'))->getPath());
    }

    public function testFilenameCanBeRetrieved(): void {
        $this->assertEquals(
            new Filename('some.phar'),
            (new Url('https://example.com/some.phar'))->getFilename()
        );
    }
}
