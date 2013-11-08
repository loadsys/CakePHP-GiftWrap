<?php

class PresenterListIterator extends ArrayIterator {
	protected $_class;
	protected $_options = array();
	protected $_controller = null;
	protected $_cache = array();

	public function __construct(
		$array,
		$class,
		$options = array(),
		$controller = null
	) {
		parent::__construct($array);
		$this->_class = $class;
		$this->_options = $options;
		$this->_controller = $controller;
	}

	public function current() {
		return $this->cache($this->key(), parent::current());
	}

	public function offsetGet($index) {
		return $this->cache($index, parent::offsetGet($index));
	}

	protected function cache($index, $value) {
		if (isset($this->_cache[$index])) {
			$val = $this->_cache[$index];
		} else {
			$val = $this->_cache[$index] = $this->wrap($value);
		}
		return $val;
	}

	protected function wrap($value) {
		return new $this->_class($value, $this->_options, $this->_controller);
	}
}
