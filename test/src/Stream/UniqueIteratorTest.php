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
class UniqueIteratorTest extends TestCase {

    public function testUnique() {
        $items = new UniqueIterator([2, 1, 3, 3, 1], UniqueIterator::SORT_NUMERIC);
        $result = [];
        foreach ($items as $item) {
            $result[] = $item;
        }
        $this->assertEquals([2, 1, 3], $result);
    }

    public function testToString() {
        $this->assertIsString((string) new UniqueIterator([0, 4, 2, 4]));
    }
}
