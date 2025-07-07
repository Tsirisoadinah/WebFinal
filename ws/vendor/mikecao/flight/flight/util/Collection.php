<?php
/**
 * Flight: An extensible micro-framework.
 *
 * @copyright   Copyright (c) 2011, Mike Cao <mike@mikecao.com>
 * @license     MIT, http://flightphp.com/license
 */

namespace flight\util;

if (!interface_exists('JsonSerializable')) {
    require_once dirname(__FILE__) . '/LegacyJsonSerializable.php';
}

class Collection implements \ArrayAccess, \Iterator, \Countable, \JsonSerializable {
    private array $data;

    public function __construct(array $data = array()) {
        $this->data = $data;
    }

    public function __get(string $key): mixed {
        return $this->data[$key] ?? null;
    }

    public function __set(string $key, mixed $value): void {
        $this->data[$key] = $value;
    }

    public function __isset(string $key): bool {
        return isset($this->data[$key]);
    }

    public function __unset(string $key): void {
        unset($this->data[$key]);
    }

    // ArrayAccess
    public function offsetExists(mixed $offset): bool {
        return isset($this->data[$offset]);
    }

    public function offsetGet(mixed $offset): mixed {
        return $this->data[$offset] ?? null;
    }

    public function offsetSet(mixed $offset, mixed $value): void {
        if (is_null($offset)) {
            $this->data[] = $value;
        } else {
            $this->data[$offset] = $value;
        }
    }

    public function offsetUnset(mixed $offset): void {
        unset($this->data[$offset]);
    }

    // Iterator
    public function rewind(): void {
        reset($this->data);
    }

    public function current(): mixed {
        return current($this->data);
    }

    public function key(): mixed {
        return key($this->data);
    }

    public function next(): void {
        next($this->data);
    }

    public function valid(): bool {
        $key = key($this->data);
        return ($key !== null && $key !== false);
    }

    // Countable
    public function count(): int {
        return count($this->data);
    }

    // JsonSerializable
    public function jsonSerialize(): mixed {
        return $this->data;
    }

    public function keys(): array {
        return array_keys($this->data);
    }

    public function getData(): array {
        return $this->data;
    }

    public function setData(array $data): void {
        $this->data = $data;
    }

    public function clear(): void {
        $this->data = array();
    }
}
