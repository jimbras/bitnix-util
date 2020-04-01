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
class LimitIteratorTest extends TestCase {

    public function testLimit() {
        $items = new LimitIterator([1, 2, 'stop' => 3, 4, 5], 3);
        $result = [];
        foreach ($items as $key => $value) {
            $result[$key] = $value;
        }
        $this->assertEquals([1, 2, 'stop' => 3], $result);
    }

    public function testToString() {
        $this->assertIsString((string) new LimitIterator([1, 2, 3], 1));
    }
}
