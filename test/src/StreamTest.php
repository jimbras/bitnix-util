<?php declare(strict_types=1);

/**
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero General Public License as
 *  published by the Free Software Foundation, either version 3 of the
 *  License, or (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Affero General Public License for more details.
 *
 *  You should have received a copy of the GNU Affero General Public License
 *  along with this program. If not, see <https://www.gnu.org/licenses/agpl-3.0.txt>.
 */

namespace Bitnix\Util;

use ArrayIterator,
    PHPUnit\Framework\TestCase;

/**
 * @version 0.1.0
 */
class StreamTest extends TestCase {

    public function testCount() {
        $stream = new Stream([1, 2, 3]);
        $this->assertEquals(3, $stream->count());

        $stream = new Stream(new ArrayIterator([1, 2, 3]));
        $this->assertEquals(3, $stream->count());
    }

    public function testFilter() {
        $items = (new Stream([1, 2, 3]))->filter(fn($el) => false)->items();
        $this->assertEquals([], $items);
    }

    public function testMap() {
        $items = (new Stream([1, 2, 3]))->map(fn($el) => 2 * $el)->items();
        $this->assertEquals([2, 4, 6], $items);
    }

    public function testSort() {
        $items = (new Stream([3, 2, 1]))->sort()->items();
        $this->assertEquals([1, 2, 3], $items);
    }

    public function testPeek() {
        $result = [];
        $fn = function($val, $key) use (&$result) {
            $result['key: ' . $key] = 'value: ' . $val;
        };
        $items = (new Stream([1, 2, 3]))->peek($fn)->items();
        $this->assertEquals([1, 2, 3], $items);
        $this->assertEquals([
            'key: 0' => 'value: 1',
            'key: 1' => 'value: 2',
            'key: 2' => 'value: 3'
        ], $result);
    }

    public function testUnique() {
        $items = (new Stream([1, 1, 3, 2, 3]))->unique()->items();
        $this->assertEquals([0 => 1, 2 => 3, 3 => 2], $items);
    }

    public function testLimit() {
        $items = (new Stream([1, 2, 3]))->limit(1)->items();
        $this->assertEquals([1], $items);
    }

    public function testSkip() {
        $items = Stream::of([1, 2, 3])->skip(1)->items();
        $this->assertEquals([1 => 2, 2 => 3], $items);
    }

    public function testCollect() {
        $items = Stream::of([1, 2, 3])->collect(fn($els) => \array_reverse($els));
        $this->assertEquals([3, 2, 1], $items);
    }

    public function testReduce() {
        $optional = Stream::of([])->reduce(fn($res, $val) => $res + $val);
        $this->assertSame(Optional::empty(), $optional);

        $optional = Stream::of([1, 2, 3])->reduce(fn($res, $val) => null);
        $this->assertFalse($optional->valid());
    }

    public function testFirst() {
        $optional = Stream::of([])->first();
        $this->assertSame(Optional::empty(), $optional);

        $optional = Stream::of([null, true, false])->first();
        $this->assertFalse($optional->valid());

        $optional = Stream::of(['foo' => 'bar', 'zig' => 'zag'])->first(true);
        $this->assertEquals(['foo' => 'bar'], $optional->value());
    }

    public function testLast() {
        $optional = Stream::of([])->last();
        $this->assertSame(Optional::empty(), $optional);

        $optional = Stream::of([null, true, false])->last();
        $this->assertFalse($optional->value());

        $optional = Stream::of(['foo' => 'bar', 'zig' => 'zag'])->last(true);
        $this->assertEquals(['zig' => 'zag'], $optional->value());
    }

    public function testKeys() {
        $items = Stream::of(['foo' => 'bar', 'zig' => 'zag'])->keys();
        $this->assertEquals(['foo', 'zig'], $items);
    }

    public function testValues() {
        $items = Stream::of(['foo' => 'bar', 'zig' => 'zag'])->values();
        $this->assertEquals(['bar', 'zag'], $items);
    }

    public function testLoop() {
        $result = [];
        Stream::of(['foo' => 'bar', 'zig' => 'zag'])->loop(function($value) use (&$result) {
            $result[] = $value;
        });
        $this->assertEquals(['bar', 'zag'], $result);
    }

    public function testForeach() {
        $result = [];
        Stream::of(['foo' => 'bar', 'zig' => 'zag'])->foreach(function($key, $value) use (&$result) {
            $result[$value] = $key;
        });
        $this->assertEquals(['bar' => 'foo', 'zag' => 'zig'], $result);
    }

    public function testEmpty() {
        $this->assertSame(Stream::empty(), Stream::empty());
    }

    public function testSeed() {
        $items = Stream::seed(1, fn($el) => $el * 2)
            ->limit(3)
            ->items();
        $this->assertEquals([1, 2, 4], $items);
    }

    public function testCall() {
        $items = Stream::call(fn() => 1)
            ->limit(3)
            ->items();
        $this->assertEquals([1, 1, 1], $items);
    }

    public function testToString() {
        $this->assertIsString((string) new Stream());
    }
}
