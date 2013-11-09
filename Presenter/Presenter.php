<?php
/**
 * Base Presenter class. Access to properties on an instance may be accessed
 * directly or through array access. Array access will proxy to the `contextKey`
 * if a property does not exist. Direct property access will proxy to the
 * Helpers object on Controller->View, so that the Presenter has access to view
 * helpers. Method access proxies to Controller->View, so the Presenter can have
 * access to the view methods.
 *
 * @author Joey Trapp <joey@loadsys.com>
 * @copyright Loadsys Web Strategies 2013
 * @version 1.0.0
 */
class Presenter implements arrayaccess {
	/**
	 * @access protected
	 * @var Array $_options Configured options for instance
	 */
	protected $_options = array();

	/**
	 * @access protected
	 * @var Array $_defaults Default options to merge with supplied options
	 */
	protected $_defaults = array('contextKey' => 'model');

	/**
	 * @access protected
	 * @var Mixed $_controller A controller object for access to View
	 */
	protected $_controller = null;

	/**
	 * New presenters are created with some data. If that data is an array, then
	 * each key is set as a public property on the instance. Any other data type
	 * is set on the presenter in the `contextKey` property (default is `model`).
	 *
	 * Only one option is valid, and that is `contextKey`. The `contextKey` is the
	 * property that array access will proxy to when a property doesn't exist on
	 * the instance for the key accessed.
	 *
	 * Presenters can also take a controller. When given a controller (or
	 * controller like object), method and property access can proxy to the view
	 * property on the controller. This way, presenter methods can access view
	 * helpers or view methods.
	 *
	 * $presenter = new Presenter(array('model' => array('one' => 1)));
	 * $presenter->model #=> array('one' => 1)
	 * $presenter['one'] #=> 1
	 *
	 * @access public
	 * @param Array $data Data for the presenter
	 * @param Array $options Options for the presenter
	 * @param Mixed $controller Controller used for access to View and Helpers
	 */
	public function __construct($data = array(), $options = array(), $controller = null) {
		$this->_options = $options + $this->_defaults;
		$this->_controller = $controller;
		if (!is_array($data)) {
			$data = array($this->_options['contextKey'] => $data);
		}
		$this->setLocalProperties($data);
	}

	/**
	 * Set a value to the property designated by the `contextKey` option.
	 *
	 * @access public
	 * @param Mixed $value The value to set in the `contextKey` property.
	 */
	public function setContext($value) {
		$this->setLocalProperties(array($this->_options['contextKey'] => $value));
	}

	/**
	 * Arrayaccess set support
	 *
	 * @access public
	 * @param Mixed $offset
	 * @param Mixed $value
	 */
	public function offsetSet($offset, $value) {
		$this->{$offset} = $value;
	}

	/**
	 * Arrayaccess get support
	 *
	 * @access public
	 * @param Mixed $offset
	 * @return Mixed
	 */
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

	/**
	 * Arrayaccess unset support
	 *
	 * @access public
	 * @param Mixed $offset
	 */
	public function offsetUnset($offset) {
		unset($this->{$offset});
	}

	/**
	 * Arrayaccess exists support
	 *
	 * @access public
	 * @param Mixed $offset
	 * @return Boolean
	 */
	public function offsetExists($offset) {
		return property_exists($this, $offset);
	}

	/**
	 * Catchall property access handler. If the controller property was set on
	 * construction, and that controller has a View property, proxy property
	 * access to that Views Helper property, so access to view helpers is
	 * possible.
	 *
	 * @access public
	 * @param String $property Property attempting to be accessed
	 * @throws Error when property does not exist in Helpers
	 * @return Mixed
	 */
	public function __get($property) {
		if ($this->_controller && $this->_controller->View) {
			try {
				$ret = $this->_controller->View->Helpers->{$property};
			} catch (Exception $e) {
				trigger_error('Undefined property: '.get_class($this).'::$'.$property);
			}
			return $ret;
		}
	}

	/**
	 * Catchall method access handler. If controller property exists, and it has
	 * a View property, then the method will be called on that View object.
	 *
	 * @access public
	 * @param String $method Method attempting to be called
	 * @throws Error when method does not exist on View
	 * @return Mixed
	 */
	public function __call($method, $args = array()) {
		if ($this->_controller && $this->_controller->View) {
			try {
				$ret = call_user_func_array(array($this->_controller->View, $method), $args);
			} catch (Exception $e) {
				trigger_error('Call to undefined method '.get_class($this).'::'.$method.'()');
			}
			return $ret;
		}
	}

	/**
	 * @access protected
	 * @param Array $data Data to be set to properties on this instance
	 */
	protected function setLocalProperties($data = array()) {
		if (!is_array($data)) { return; }
		foreach ($data as $key => $value) {
			if (!is_numeric($key)) {
				$this->{$key} = $value;
			}
		}
	}
}
