<?php declare(strict_types = 1);
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

use function json_encode;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \PharIo\Phive\JsonData
 */
class JsonDataTest extends TestCase {
    public function testThrowsExceptionIfRawValueIsNotJson(): void {
        $this->expectException(InvalidArgumentException::class);
        new JsonData('foo');
    }

    public function testThrowsExceptionIfRawValueIsNotAJsonObjectOrArray(): void {
        $this->expectException(InvalidArgumentException::class);
        new JsonData(json_encode('foo'));
    }

    public function testGetRawReturnsExpectedValue(): void {
        $raw = json_encode(['foo' => 'bar']);

        $data = new JsonData($raw);
        $this->assertSame($raw, $data->getRaw());
    }

    public function testGetParsedReturnsExpectedValue(): void {
        $input = ['foo' => 'bar'];
        $raw   = json_encode($input);
        $data  = new JsonData($raw);

        $this->assertEquals($input, $data->getParsed());
    }

    public function testHasFragmentReturnsFalse(): void {
        $raw  = json_encode(['foo' => 'bar']);
        $data = new JsonData($raw);

        $this->assertFalse($data->hasFragment('foobar'));
    }

    public function testHasFragmentReturnsTrue(): void {
        $raw  = json_encode(['foo' => 'bar']);
        $data = new JsonData($raw);

        $this->assertTrue($data->hasFragment('foo'));
    }

    public function testGetFragmentThrowsExceptionIfFragmentDoesNotExist(): void {
        $raw = json_encode(
            [
                'parent' => [
                    'child' => [
                        'foo' => 'bar'
                    ]
                ]
            ]
        );

        $data = new JsonData($raw);
        $this->expectException(InvalidArgumentException::class);

        $data->getFragment('parent.child.foobar');
    }

    public function testGetFragmentReturnsExpectedValue(): void {
        $raw = json_encode(
            [
                'parent' => [
                    'child' => [
                        'foo' => 'bar'
                    ]
                ]
            ]
        );

        $data = new JsonData($raw);

        $this->assertSame('bar', $data->getFragment('parent.child.foo'));
        $this->assertEquals(['foo' => 'bar'], $data->getFragment('parent.child'));
    }
}
