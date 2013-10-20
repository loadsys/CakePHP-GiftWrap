<?php

class Presenter implements arrayaccess {
	public $defaultProperties = array();
	public $requireProperties = array();

	protected $_options = array();
	protected $_defaults = array('contextKey' => 'model');
	protected $_controller = null;

	public function __construct($data = array(), $options = array(), $controller = null) {
		$this->_options = $options + $this->_defaults;
		$this->_controller = $controller;
		if (!is_array($data)) {
			$data = array($this->_options['contextKey'] => $data);
		}
		$this->setLocalProperties($this->defaultProperties);
		$this->setLocalProperties($data);
		$this->checkRequiredProperties($this->requireProperties, $data);
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

	protected function setLocalProperties($data = array()) {
		if (!is_array($data)) {
			return;
		}
		foreach ($data as $key => $value) {
			if (!is_numeric($key)) {
				$this->{$key} = $value;
			}
		}
	}

	protected function checkRequiredProperties($required, $data) {
		if (is_array($required) && !empty($required)) {
			$diff = array_diff($required, array_keys($data));
			if (!empty($diff)) {
				$class = get_class($this);
				$missing = join(', ', $diff);
				throw new LogicException($class . ' missing properties: ' . $missing);
			}
		}
	}
}
