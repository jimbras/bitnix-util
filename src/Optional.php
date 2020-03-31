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

use UnexpectedValueException;

/**
 * @version 0.1.0
 */
final class Optional {

    /**
     * @var Optional
     */
    private static ?Optional $empty = null;

    /**
     * @var mixed
     */
    private $value;

    /**
     * @param mixed $value
     */
    private function __construct($value = null) {
        $this->value = $value;
    }

    /**
     * @return bool
     */
    public function valid() : bool {
        return null !== $this->value;
    }

    /**
     * @return bool
     */
    public function invalid() : bool {
        return null === $this->value;
    }

    /**
     * @return mixed
     * @throws UnexpectedValueException
     */
    public function value() {
        if (null === $this->value) {
            throw new UnexpectedValueException('Unexpected null value');
        }
        return $this->value;
    }

    /**
     * @param mixed $other
     * @return mixed
     */
    public function orValue($other) {
        return $this->value ?? $other;
    }

    /**
     * @param callable $provider
     * @return mixed
     */
    public function orCall(callable $provider) {
        return $this->value ?? $provider();
    }

    /**
     * @param callable $provider
     * @return static
     */
    public function or(callable $provider) : Optional {
        return null === $this->value ? $provider() : $this;
    }

    /**
     * @param callable $provider
     * @throws \Throwable
     */
    public function orFail(callable $provider) {
        if (null === $this->value) {
            throw $provider();
        }
        return $this->value;
    }

    /**
     * @param callable $handler
     * @param null|callable $empty
     */
    public function call(callable $handler, callable $empty = null) : void {
        if (null !== $this->value) {
            $handler($this->value);
        } else if ($empty) {
            $empty();
        }
    }

    /**
     * @param callable $filter
     * @return static
     */
    public function filter(callable $filter) : self {
        if (null === $this->value || $filter($this->value)) {
            return $this;
        }
        return self::empty();
    }

    /**
     * @param callable $mapper
     * @return static
     */
    public function map(callable $mapper) : self {
        return null === $this->value ? $this : self::ofNullable($mapper($this->value));
    }

    /**
     * @param Optional $other
     * @return bool
     */
    public function equals(Optional $other) : bool {
        return $this->value === $other->value;
    }

    /**
     * @return string
     */
    public function __toString() : string {

        $value = null === $this->value
            ? 'null'
            : (\is_object($this->value)
                ? (\is_callable([$this->value, '__toString'])
                    ? (string) $this->value
                    : \get_class($this->value))
                : \gettype($this->value));

        return \sprintf('%s (value=%s)', self::CLASS, $value);
    }

    /**
     * @return self
     */
    public static function empty() : self {
        return self::$empty ?: self::$empty = new self();
    }

    /**
     * @param mixed $value
     * @throws UnexpectedValueException
     */
    public static function of($value) : self {
        if (null === $value) {
            throw new UnexpectedValueException('Unexpected null value');
        }

        return new self($value);
    }

    /**
     * @param mixed $value
     * @return self
     */
    public static function ofNullable($value) : self {
        return $value === null ? self::empty() : self::of($value);
    }

}
