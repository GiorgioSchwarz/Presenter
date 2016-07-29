<?php

namespace Moln\Presenter;

class HtmlPresenter implements \Iterator
{
	private $object;
	private $implementsIterator;

	public function __construct($object)
	{
		if ( ! is_object($object)) {
			throw new \InvalidArgumentException('HtmlPresenter expects an object.');
		}
		
		if ($object instanceof static) {
			throw new \InvalidArgumentException('Cannot pass another HtmlPresenter as an object.');
		}
		
		$this->object = $object;
		$this->implementsIterator = $object instanceof \Iterator;
	}

	public function getRaw()
	{
		return $this->object;
	}

	public function getTargetClass($targetClass = null)
	{
		if ($targetClass) {
			return ($this->object instanceof $targetClass);
		}

		return $this->escape(get_class($this->object));
	}

	public function __call($methodName, $args)
	{
		$rawValue = call_user_func_array([$this->object, $methodName], $args);

		if ($rawValue === $this->object) {
			return $this;
		}
		
		return $this->escape($rawValue);
	}

	public function __get($property)
	{
		$rawValue = $this->object->$property;
		
		if ($rawValue) {
			return $this->escape($rawValue);
		}
	}

	// !Iterator imp
	public function rewind() {
       if ($this->implementsIterator) {
       		return $this->object->rewind();
       }
    }

    public function current() {
		if ($this->implementsIterator) {
			return $this->escape($this->object->current());
		}
		return $this;
    }

    public function key() {
        if ($this->implementsIterator) {
			return $this->object->key();
		}
		return 0;
    }

    public function next() {
    	if ($this->implementsIterator) {
			return $this->object->next();
		}
    }

    function valid() {
    	if ($this->implementsIterator) {
			return $this->object->valid();
		}
		return false;
    }

    // !Escape methods

	private function escape($value)
	{
		if (is_object($value)) {
			return new static($value);
		}

		if (is_array($value)) {
			return $this->escapeArray($value);
		}

		return htmlentities($value, ENT_QUOTES, 'UTF-8');
	}

	private function escapeArray(array $array)
	{
		$clear = [];
		
		foreach ($array as $key => $value) {
			$clear[$this->escape($key)] = $this->escape($value);
		}

		return $clear;
	}
}