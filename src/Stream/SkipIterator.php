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

use IteratorAggregate;

/**
 * @version 0.1.0
 */
final class SkipIterator implements IteratorAggregate {

    /**
     * @var iterable
     */
    private iterable $items;

    /**
     * @var int
     */
    private int $skip;

    /**
     * @param iterable $items
     * @param int $limit
     */
    public function __construct(iterable $items, int $skip) {
        $this->items = $items;
        $this->skip = \max(0, $skip);
    }

    /**
     * @return iterable
     */
    public function getIterator() : iterable {
        $skip = $this->skip;
        foreach ($this->items as $key => $value) {
            if ($skip-- > 0) {
                continue;
            }
            yield $key => $value;
        }
    }

    /**
     * @return string
     */
    public function __toString() : string {
        return self::CLASS;
    }
}
