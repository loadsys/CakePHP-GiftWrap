<?php

class PresenterListIterator extends ArrayIterator {
	protected $_class;
	protected $_extra = array();
	protected $_options;
	protected $_controller = null;
	protected $_cache = array();

	public function __construct(
		$array,
		$class,
		$extra = array(),
		$options = array(),
		$controller = null
	) {
		parent::__construct($array);
		$this->_class = $class;
		$this->_extra = $extra;
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
		$options = array('performRequireCheck' => false) + $this->_options;
		$presenter = new $this->_class($this->_extra, $options, $this->_controller);
		$presenter->setContext($value);
		$presenter->checkRequireProperties();
		return $presenter;
	}
}