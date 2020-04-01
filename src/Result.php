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

/**
 * @version 0.1.0
 */
final class Result {

    /**
     * @var mixed
     */
    private $value;

    /**
     * @var array
     */
    private array $errors;

    /**
     * @var bool
     */
    private bool $valid;

    /**
     * @param mixed $value
     * @param array $errors
     */
    private function __construct($value = null, array $errors = []) {
        $this->value = $value;
        $this->errors = $errors;
        $this->valid = empty($errors);
    }

    /**
     * @return bool
     */
    public function valid() : bool {
        return $this->valid;
    }

    /**
     * @return array
     */
    public function errors() : array {
        return $this->errors;
    }

    /**
     * @return mixed
     * @throws ResultError
     */
    public function value() {
        if ($this->valid) {
            return $this->value;
        }
        throw new ResultError($this->errors);
    }

    /**
     * @param mixed $other
     * @return mixed
     */
    public function orValue($other) {
        return $this->valid ? $this->value : $other;
    }

    /**
     * @param callable $provider
     * @return mixed
     */
    public function orCall(callable $provider) {
        return $this->valid ? $this->value : $provider($this->errors);
    }

    /**
     * @param callable $thrower
     * @return mixed
     * @throws \Throwable
     */
    public function orFail(callable $thrower) {
        if ($this->valid) {
            return $this->value;
        }

        throw $thrower($this->errors);
    }

    /**
     * @param callable $mapper
     * @return static
     */
    public function map(callable $mapper) : self {
        if ($this->valid) {
            $result = clone $this;
            $result->value = $mapper($this->value);
            return $result;
        }
        return $this;
    }

    /**
     * @return string
     */
    public function __toString() : string {
        return \sprintf(
            '%s (valid=%s)',
                self::CLASS,
                $this->valid ? 'true' : 'false'
        );
    }

    /**
     * @param mixed $value
     * @return self
     */
    public static function ok($value = null) : self {
        return new self($value);
    }

    /**
     * @param string ...$errors
     * @return self
     */
    public static function error(string ...$errors) : self {
        return new self(null, $errors ?: ['Unknow error']);
    }
}
