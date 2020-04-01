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

namespace Bitnix\Util\Stream;

use PHPUnit\Framework\TestCase;

/**
 * @version 0.1.0
 */
class SortIteratorTest extends TestCase {

    public function testSortList() {
        $items = new SortIterator([6, 0, 4]);
        $result = [];
        foreach ($items as $key => $item) {
            $result[$key] = $item;
        }
        $this->assertEquals([0, 4, 6], $result);
    }

    public function testCustomSortList() {
        $items = new SortIterator([6, 0, 4], fn($a, $b) => $b <=> $a);
        $result = [];
        foreach ($items as $key => $item) {
            $result[$key] = $item;
        }
        $this->assertEquals([6, 4, 0], $result);
    }

    public function testSortMap() {
        $items = new SortIterator(['a' => 6, 'b' => 0, 'c' => 4], null, true);
        $result = [];
        foreach ($items as $key => $item) {
            $result[$key] = $item;
        }
        $this->assertEquals(['b' => 0, 'c' => 4, 'a' => 6], $result);
    }

    public function testCustomSortMap() {
        $items = new SortIterator(['a' => 6, 'b' => 0, 'c' => 4], fn($a, $b) => $b <=> $a, true);
        $result = [];
        foreach ($items as $key => $item) {
            $result[$key] = $item;
        }
        $this->assertSame(['a' => 6, 'c' => 4, 'b' => 0], $result);
    }

    public function testToString() {
        $this->assertIsString((string) new SortIterator([0, 4, 2]));
    }
}
