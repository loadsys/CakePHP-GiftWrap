<?php
/**
 * An ArrayIterator that wraps the elements of a supplied array in a specified
 * Presenter class.
 *
 * @author Joey Trapp <joey@loadsys.com>
 * @copyright Loadsys Web Strategies 2013
 * @version 1.0.0
 */
class PresenterListIterator extends ArrayIterator {
	/**
	 * @access protected
	 * @var Class $_class
	 */
	protected $_class;

	/**
	 * @access protected
	 * @var Array $_options
	 */
	protected $_options = array();

	/**
	 * @access protected
	 * @var Object $_controller
	 */
	protected $_controller = null;

	/**
	 * @access protected
	 * @var Array $_cache
	 */
	protected $_cache = array();

	/**
	 * Create the iterator with the array backing it, a Presenter class to wrap
	 * each iteration in, and options and a controller for each Presenter
	 * instance.
	 *
	 * @access public
	 * @param Array $array
	 * @param Class $class
	 * @param Array $options
	 * @param Object $controller
	 */
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

	/**
	 * ArrayIterator current support
	 *
	 * @access public
	 * @return Mixed
	 */
	public function current() {
		return $this->cache($this->key(), parent::current());
	}

	/**
	 * ArrayIterator offsetGet support. Fetches from cached created presenter
	 * instances.
	 *
	 * @access public
	 * @param Mixed $index
	 * @return Mixed
	 */
	public function offsetGet($index) {
		return $this->cache($index, parent::offsetGet($index));
	}

	/**
	 * @access protected
	 * @param Mixed $index
	 * @param Mixed $value
	 * @return Presenter
	 */
	protected function cache($index, $value) {
		if (isset($this->_cache[$index])) {
			$val = $this->_cache[$index];
		} else {
			$val = $this->_cache[$index] = $this->wrap($value);
		}
		return $val;
	}

	/**
	 * @access protected
	 * @param Mixed $value
	 * @return Presenter
	 */
	protected function wrap($value) {
		return new $this->_class($value, $this->_options, $this->_controller);
	}
}
