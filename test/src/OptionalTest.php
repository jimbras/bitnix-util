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

use UnexpectedValueException,
    PHPUnit\Framework\TestCase;

/**
 * @version 0.1.0
 */
class OptionalTest extends TestCase {

    public function testEmptyIsSingleton() {
        $this->assertSame(
            Optional::empty(),
            Optional::empty()
        );
    }

    public function testOfCreatesObject() {
        $opt = Optional::of('foo');
        $this->assertTrue($opt->valid());
        $this->assertFalse($opt->invalid());
        $this->assertEquals('foo', $opt->value());
    }

    public function testOfWithNullThrowsException() {
        $this->expectException(UnexpectedValueException::CLASS);
        Optional::of(null);
    }

    public function testOfNullableCreatesObjectWithPossibleNullValue() {
        $opt = Optional::safe(null);
        $this->assertFalse($opt->valid());
        $this->assertTrue($opt->invalid());

        $opt = Optional::safe('foo');
        $this->assertTrue($opt->valid());
        $this->assertFalse($opt->invalid());
        $this->assertEquals('foo', $opt->value());
    }

    public function testCallingValueOnInvalidOptionalThrowsException() {
        $this->expectException(UnexpectedValueException::CLASS);
        Optional::safe(null)->value();
    }

    public function testOrValue() {
        $opt = Optional::safe(null);
        $this->assertEquals('foo', $opt->orValue('foo'));
    }

    public function testOrCall() {
        $opt = Optional::safe(null);
        $this->assertEquals('foo', $opt->orCall(fn() => 'foo'));
    }

    public function testOr() {
        $opt = Optional::safe(null);
        $other = Optional::of('foo');
        $this->assertSame($other, $opt->or(fn() => $other));
    }

    public function testOrFailReturnsValue() {
        $this->assertEquals('foo', Optional::of('foo')->orFail(function() { return new \RuntimeException(); }));
    }

    public function testOrFailThrowsException() {
        $this->expectException(\RuntimeException::CLASS);
        Optional::safe(null)->orFail(function() { return new \RuntimeException(); });
    }

    public function testCall() {
        $called = 0;
        $counter1 = function() use (&$called) { ++$called; };
        $counter2 = function() use (&$called) { --$called; };

        Optional::safe('foo')->call($counter1);
        $this->assertEquals(1, $called);

        Optional::safe(null)->call($counter1, $counter2);
        $this->assertEquals(0, $called);
    }

    public function testFilter() {
        $opt = Optional::safe(null);
        $this->assertSame($opt, $opt->filter(fn() => true));
        $this->assertSame($opt, $opt->filter(fn() => false));

        $opt = Optional::safe('foo');
        $this->assertSame($opt, $opt->filter(fn() => true));
        $this->assertSame(Optional::empty(), $opt->filter(fn() => false));
    }

    public function testMap() {
        $this->assertEquals(
            'baz',
            Optional::of('bar')->map(fn() => 'baz')->value()
        );
        $this->assertSame(
            Optional::empty(),
            Optional::safe(null)->map(fn() => 'baz')
        );
    }

    public function testEquals() {
        $this->assertFalse(Optional::of('foo')->equals(Optional::of('bar')));
        $this->assertTrue(Optional::of('foo')->equals(Optional::of('foo')));
    }

    public function testStream() {
        $stream = Optional::safe(null)->stream();
        $this->assertSame(Stream::empty(), $stream);

        $items = Optional::of(1)->stream()->items();
        $this->assertEquals([1], $items);

        $items = Optional::of([1, 2, 3])->stream()->items();
        $this->assertEquals([1, 2, 3], $items);
    }

    public function testToString() {
        $this->assertStringContainsString(
            'value=' . self::CLASS, (string) Optional::of($this)
        );
        $this->assertStringContainsString(
            'value=null', (string) Optional::safe(null)
        );
        $this->assertStringContainsString(
            'value=array', (string) Optional::of([])
        );
        $this->assertStringContainsString(
            'value=foo', (string) Optional::of(new class() { public function __toString() { return 'foo'; }})
        );
    }
}
