<?php

class PresenterListIterator extends ArrayIterator {
	protected $_class;
	protected $_options;
	protected $_cache = array();

	public function __construct($array, $class, $options = array()) {
		parent::__construct($array);
		$this->_class = $class;
		$this->_options = $options;
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
		$presenter = new $this->_class(array(), $this->_options);
		$presenter->setContent($value);
		return $presenter;
	}
}