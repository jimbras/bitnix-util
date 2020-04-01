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

use ArrayIterator,
    IteratorAggregate;

/**
 * @version 0.1.0
 */
final class UniqueIterator implements IteratorAggregate {

    const SORT_REGULAR       = \SORT_REGULAR;
    const SORT_NUMERIC       = \SORT_NUMERIC;
    const SORT_STRING        = \SORT_STRING;
    const SORT_LOCALE_STRING = \SORT_LOCALE_STRING;

    private const FLAGS = [
        self::SORT_REGULAR       => self::SORT_REGULAR,
        self::SORT_NUMERIC       => self::SORT_NUMERIC,
        self::SORT_STRING        => self::SORT_STRING,
        self::SORT_LOCALE_STRING => self::SORT_LOCALE_STRING
    ];

    /**
     * @var iterable
     */
    private iterable $items;

    /**
     * @var int
     */
    private int $flag;

    /**
     * @param iterable $items
     * @param int $flag
     */
    public function __construct(iterable $items, int $flag = self::SORT_REGULAR) {
        $this->items = $items;
        $this->flag = self::FLAGS[$flag] ?? self::SORT_REGULAR;
    }

    /**
     * @return iterable
     */
    public function getIterator() : iterable {
        $items = \is_array($this->items)
            ? $this->items
            : \iterator_to_array($this->items);

        return new ArrayIterator(\array_unique($items, $this->flag));
    }

    /**
     * @return string
     */
    public function __toString() : string {
        return self::CLASS;
    }
}
