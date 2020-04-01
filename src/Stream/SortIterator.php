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
final class SortIterator implements IteratorAggregate {

    /**
     * @var iterable
     */
    private iterable $items;

    /**
     * @var null|callable
     */
    private $sorter;

    /**
     * @var callable
     */
    private $callback;

    /**
     * @param iterable $items
     * @param null|callable $sorter
     * @param bool $assoc
     */
    public function __construct(iterable $items, callable $sorter = null, bool $assoc = false) {
        $this->items = $items;
        $this->sorter = $sorter;

        if ($sorter) {
            $cb = $assoc ? 'uasort' : 'usort';
        } else {
            $cb = $assoc ? 'asort' : 'sort';
        }

        $this->callback = [$this, $cb];
    }

    /**
     * @return iterable
     */
    public function getIterator() : iterable {
        $items = \is_array($this->items)
            ? $this->items
            : \iterator_to_array($this->items);

        return new ArrayIterator(($this->callback)($items));
    }

    /**
     * @param array $items
     * @return array
     */
    private function sort(array $items) : array {
        sort($items);
        return $items;
    }

    /**
     * @param array $items
     * @return array
     */
    private function asort(array $items) : array {
        asort($items);
        return $items;
    }

    /**
     * @param array $items
     * @return array
     */
    private function usort(array $items) : array {
        usort($items, $this->sorter);
        return $items;
    }

    /**
     * @param array $items
     * @return array
     */
    private function uasort(array $items) : array {
        uasort($items, $this->sorter);
        return $items;
    }

    /**
     * @return string
     */
    public function __toString() : string {
        return self::CLASS;
    }
}
