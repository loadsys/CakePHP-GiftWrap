<?php

class Presenter implements arrayaccess {
	protected $_options = array();
	protected $_defaults = array('contextKey' => 'model');
	protected $_controller = null;

	public function __construct($data = array(), $options = array(), $controller = null) {
		$this->_options = $options + $this->_defaults;
		$this->_controller = $controller;
		if (is_array($data)) {
			foreach ($data as $key => $value) {
				$this->{$key} = $value;
			}
		} else {
			$this->{$this->_options['contextKey']} = $data;
		}
	}

	public function setContext($value) {
		$this->{$this->_options['contextKey']} = $value;
	}

	public function offsetSet($offset, $value) {
		$this->{$offset} = $value;
	}

	public function offsetGet($offset) {
		if (property_exists($this, $offset)) {
			$value = $this->{$offset};
		} else if (
			is_array($this->{$this->_options['contextKey']}) &&
			array_key_exists($offset, $this->{$this->_options['contextKey']})
		) {
			$value = $this->{$this->_options['contextKey']}[$offset];
		} else {
			trigger_error("Undefined index '$offset'");
		}
		return $value;
	}

	public function offsetUnset($offset) {
		unset($this->{$offset});
	}

	public function offsetExists($offset) {
		return property_exists($this, $offset);
	}

	public function __get($name) {
		if ($this->_controller && $this->_controller->View) {
			return $this->_controller->View->Helpers->{$name};
		}
	}

	public function __call($method, $args = array()) {
		if ($this->_controller && $this->_controller->View) {
			return call_user_func_array(array($this->_controller->View, $method), $args);
		}
	}
}
