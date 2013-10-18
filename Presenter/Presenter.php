<?php

class Presenter implements arrayaccess {
	private $_options = array();
	private $_defaults = array('content' => 'content');

	public function __construct($data = array(), $options = array()) {
		$this->_options = $options + $this->_defaults;
		if (is_array($data)) {
			foreach ($data as $key => $value) {
				$this->{$key} = $value;
			}
		} else {
			$this->{$this->_options['content']} = $data;
		}
	}

	public function setContent($value) {
		$this->{$this->_options['content']} = $value;
	}

	public function offsetSet($offset, $value) {
		$this->{$offset} = $value;
	}

	public function offsetGet($offset) {
		try {
			$value = $this->{$offset};
		}	catch (Exception $e) {
			try {
				if (is_array($this->{$this->_options['content']})) {
					$value = $this->{$this->_options['content']}[$offset];
				} else {
					throw $e;
				}
			} catch (Exception $f) {
				throw $e;
			}
		}
		return $value;
	}

	public function offsetUnset($offset) {
		unset($this->{$offset});
	}

	public function offsetExists($offset) {
		return property_exists($this, $offset);
	}
}
