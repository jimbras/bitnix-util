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

use PHPUnit\Framework\TestCase;

/**
 * @version 0.1.0
 */
class ResultTest extends TestCase {

    public function testOk() {
        $result = Result::ok();
        $this->assertTrue($result->valid());
        $this->assertNull($result->value());
        $this->assertEquals([], $result->errors());

        $result = Result::ok('foo');
        $this->assertEquals('foo', $result->value());
    }

    public function testError() {
        $result = Result::error();
        $this->assertFalse($result->valid());
        $this->assertEquals(['Unknow error'], $result->errors());

        $result = Result::error('Kaput');
        $this->assertEquals(['Kaput'], $result->errors());

        try {
            $result->value();
            $this->fail('Expected exception not thrown');
        } catch (ResultError $x) {
            $this->assertEquals(['Kaput'], $x->getErrors());
        }
    }

    public function testOrValue() {
        $value = Result::ok('foo')->orValue('bar');
        $this->assertEquals('foo', $value);

        $value = Result::error()->orValue('bar');
        $this->assertEquals('bar', $value);
    }

    public function testOrCall() {
        $value = Result::ok('foo')->orCall(fn($errors) => \implode(', ', $errors));
        $this->assertEquals('foo', $value);

        $value = Result::error('Kaput', 'Bang')->orCall(fn($errors) => \implode(', ', $errors));
        $this->assertEquals('Kaput, Bang', $value);
    }

    public function testOrFail() {
        $value = Result::ok('foo')->orFail(function($errors) { throw new \RuntimeException(\implode(', ', $errors)); });
        $this->assertEquals('foo', $value);

        try {
            Result::error('Kaput', 'Bang')->orFail(function($errors) { throw new \RuntimeException(\implode(', ', $errors)); });
            $this->fail('Expected exception not thrown');
        } catch (\RuntimeException $x) {
            $this->assertEquals('Kaput, Bang', $x->getMessage());
        }
    }

    public function testMap() {
        $result = Result::ok('foo');
        $mapped = $result->map(fn($el) => \strrev($el));
        $this->assertNotSame($result, $mapped);
        $this->assertEquals('oof', $mapped->value());

        $result = Result::error('Kaput');
        $mapped = $result->map(fn($el) => \strrev($el));
        $this->assertSame($result, $mapped);
    }

    public function testToString() {
        $this->assertStringContainsString('valid=true', (string) Result::ok());
        $this->assertStringContainsString('valid=false', (string) Result::error());
    }
}
