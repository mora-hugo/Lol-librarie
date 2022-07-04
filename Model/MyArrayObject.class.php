<?php
    abstract class MyArrayObject implements IteratorAggregate, ArrayAccess {
        protected Array $objects;

        public function __construct($objects) {
            foreach($objects as $object)
            {
                if(!$this->isMyClass($object)) {
                    $this->throw();
                }
                $this->add($object);
            }
        }

        protected abstract function isMyClass($object) : bool;
        public function count() : int {
            return count($this->objects);
        }
        public function getArray() : Array {
            return $this->objects;
        }

        public function get(int $index) : ?Object {
            if(isset($this->objects[$index])) {
                return $this->objects[$index];
            }
            return null;
        }
        protected function throw() : void {
            throw new InvalidArgumentException("Array need ".get_class($this)." type");
        }
        public function add(Object $object) : void {
            $this->objects[] = $object;
        }


        public function getIterator(): Traversable
        {
            return new ArrayIterator($this->objects);
        }

        public function offsetExists(mixed $offset): bool
        {
            return isset($this->objects[$offset]);
        }

        public function offsetGet(mixed $offset): mixed
        {
            return $this->objects[$offset];
        }

        public function offsetSet(mixed $offset, mixed $value): void
        {
            if (is_null($offset)) {
                $this->objects[] = $value;
            } else {
                $this->objects[$offset] = $value;
            }
        }

        public function offsetUnset(mixed $offset): void
        {
            unset($this->objects[$offset]);
        }
    }