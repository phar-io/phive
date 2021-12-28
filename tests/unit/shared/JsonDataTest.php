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

    public function testTryGetFragmentReturnsFalse(): void {
        $raw  = json_encode(['foo' => 'bar']);
        $data = new JsonData($raw);

        $this->assertFalse($data->tryGetFragment('foobar'));
    }

    public function testTryGetFragmentReturnsTrue(): void {
        $raw  = json_encode(['foo' => 'bar']);
        $data = new JsonData($raw);

        $this->assertTrue($data->tryGetFragment('foo'));
    }

    public function testTryGetFragmentReturnsExpectedValue(): void {
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

        $this->assertTrue($data->tryGetFragment('parent.child.foo', $fragment));
        $this->assertSame('bar', $fragment);
        $this->assertTrue($data->tryGetFragment('parent.child', $fragment));
        $this->assertEquals(['foo' => 'bar'], $fragment);
    }
}
