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
final class SeedIterator implements IteratorAggregate {

    /**
     * @var mixed
     */
    private $seed;

    /**
     * @var callable
     */
    private $provider;

    /**
     * @param mixed $seed
     * @param callable $provider
     */
    public function __construct($seed, callable $provider) {
        $this->seed = $seed;
        $this->provider = $provider;
    }

    /**
     * @return iterable
     */
    public function getIterator() : iterable {
        $value = $this->seed;
        do {
            yield $value;
            $value = ($this->provider)($value);
        } while (true);
    }

    /**
     * @return string
     */
    public function __toString() : string {
        return self::CLASS;
    }
}
