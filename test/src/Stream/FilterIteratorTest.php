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
class FilterIteratorTest extends TestCase {

    public function testFilterByValue() {
        $items = new FilterIterator([1, 2, 3, 4], fn($el) => $el === 4);
        $result = [];
        foreach ($items as $item) {
            $result[] = $item;
        }
        $this->assertEquals([4], $result);
    }

    public function testFilterByKey() {
        $items = new FilterIterator([1, 2, 3, 4], fn($el, $key) => $key === 1);
        $result = [];
        foreach ($items as $item) {
            $result[] = $item;
        }
        $this->assertEquals([2], $result);
    }

    public function testToString() {
        $this->assertIsString((string) new FilterIterator([], fn($el) => true));
    }
}
