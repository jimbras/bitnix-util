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

use Bitnix\Util\Stream\CallIterator,
    Bitnix\Util\Stream\FilterIterator,
    Bitnix\Util\Stream\LimitIterator,
    Bitnix\Util\Stream\MapIterator,
    Bitnix\Util\Stream\PeekIterator,
    Bitnix\Util\Stream\SeedIterator,
    Bitnix\Util\Stream\SkipIterator,
    Bitnix\Util\Stream\SortIterator,
    Bitnix\Util\Stream\UniqueIterator;

/**
 * @version 0.1.0
 */
final class Stream {

    /**
     * @var iterable
     */
    private iterable $items;

    /**
     * @var Stream
     */
    private static ?Stream $empty = null;

    /**
     * @param iterable $items
     */
    public function __construct(iterable $items = []) {
        $this->items = $items;
    }

    /**
     * @return int
     */
    public function count() : int {
        return \is_array($this->items)
            ? \count($this->items)
            : \iterator_count($this->items);
    }

    /**
     * @param callable $filter
     * @return self
     */
    public function filter(callable $filter) : self {
        return new self(new FilterIterator($this->items, $filter));
    }

    /**
     * @param callable $mapper
     * @return self
     */
    public function map(callable $mapper) : self {
        return new self(new MapIterator($this->items, $mapper));
    }

    /**
     * @param bool $assoc
     * @param null|callable $sorter
     * @return self
     */
    public function sort(bool $assoc = false, callable $sorter = null) : self {
        return new self(new SortIterator($this->items, $sorter, $assoc));
    }

    /**
     * @param callable $handler
     * @return self
     */
    public function peek(callable $handler) : self {
        return new self(new PeekIterator($this->items, $handler));
    }

    /**
     * @param int $flag
     */
    public function unique(int $flag = UniqueIterator::SORT_REGULAR) : self {
        return new self(new UniqueIterator($this->items, $flag));
    }

    /**
     * @param int $limit
     * @return self
     */
    public function limit(int $limit) : self {
        return new self(new LimitIterator($this->items, $limit));
    }

    /**
     * @return int $count
     * @return self
     */
    public function skip(int $count) : self {
        return new self(new SkipIterator($this->items, $count));
    }

    /**
     * @param callable $collector
     * @return mixed
     */
    public function collect(callable $collector) {
        return $collector($this->items());
    }

    /**
     * @param callable $reducer
     * @param mixed $first
     * @return Optional
     */
    public function reduce(callable $reducer, $initial = null) : Optional {
        $items = $this->items();
        return $items
            ? Optional::safe(\array_reduce($items, $reducer, $initial))
            : Optional::empty();
    }

    /**
     * @param bool $assoc
     * @return Optional
     */
    public function first(bool $assoc = false) : Optional {
        $items = $this->items();

        if (!$items) {
            return Optional::empty();
        }

        $key = \array_key_first($items);

        return $assoc
            ? Optional::of([$key => $items[$key]])
            : Optional::safe($items[$key]);
    }

    /**
     * @param bool $assoc
     * @return Optional
     */
    public function last(bool $assoc = false) : Optional {
        $items = $this->items();

        if (!$items) {
            return Optional::empty();
        }

        $key = \array_key_last($items);

        return $assoc
            ? Optional::of([$key => $items[$key]])
            : Optional::safe($items[$key]);
    }

    /**
     * @return array
     */
    public function keys() : array {
        return \array_keys($this->items());
    }

    /**
     * @return array
     */
    public function values() : array {
        return \array_values($this->items());
    }

    /**
     * @return array
     */
    public function items() : array {

        $items = \is_array($this->items)
            ? $this->items
            : \iterator_to_array($this->items);

        $this->items = [];

        return $items;
    }

    /**
     * @param callable $handler
     */
    public function loop(callable $handler) : void {
        foreach ($this->items() as $item) {
            $handler($item);
        }
    }

    /**
     * @param callable $handler
     */
    public function foreach(callable $handler) : void {
        foreach ($this->items() as $key => $value) {
            $handler($key, $value);
        }
    }

    /**
     * @return string
     */
    public function __toString() : string {
        return self::CLASS;
    }

    /**
     * @return self
     */
    public static function empty() : self {
        return self::$empty ?? self::$empty = new self();
    }

    /**
     * @param iterable $items
     * @return self
     */
    public static function of(iterable $items) : self {
        return new self($items);
    }

    /**
     * @param mixed $seed
     * @param callable $provider
     * @return self
     */
    public static function seed($seed, callable $provider) : self {
        return new self(new SeedIterator($seed, $provider));
    }

    /**
     * @param callable $provider
     * @return self
     */
    public static function call(callable $provider) : self {
        return new self(new CallIterator($provider));
    }
}
