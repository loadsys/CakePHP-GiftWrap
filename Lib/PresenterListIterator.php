<?php

class PresenterListIterator extends ArrayIterator {
	private $_class;
	private $_options;
	private $_cache = array();

	public function __construct($array, $class, $options = array()) {
		parent::__construct($array);
		$this->_class = $class;
		$this->_options = $options;
	}

	public function current() {
		return $this->wrap(parent::current());
	}

	public function offsetGet($index) {
		if (isset($this->_cache[$index])) {
			$val = $this->_cache[$index];
		} else {
			$val = $this->_cache[$index] = $this->wrap(parent::offsetGet($index));
		}
		return $val;
	}

	private function wrap($value) {
		$presenter = new $this->_class(array(), $this->_options);
		$presenter->setContent($value);
		return $presenter;
	}
}