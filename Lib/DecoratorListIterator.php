<?php

App::uses('PresenterListIterator', 'CakePHP-GiftWrap.Lib');

/**
 * A specialized PresenterListIterator that assigns the element in each
 * iteration to the context property on the Presenter instance instead of
 * passing the data into the constructor.
 *
 * @author Joey Trapp <joey@loadsys.com>
 * @copyright Loadsys Web Strategies 2013
 * @version 1.0.0
 */
class DecoratorListIterator extends PresenterListIterator {
	/**
	 * @access protected
	 * @var Array $_extra
	 */
	protected $_extra = array();

	/**
	 * Creates an iterator using the parent constructor, and saves extra data
	 * that will be used in creating Presenter instances.
	 *
	 * @access public
	 * @param Array $array
	 * @param Class $class
	 * @param Array $extra
	 * @param Array $options
	 * @param Object $controller
	 */
	public function __construct(
		$array,
		$class,
		$extra = array(),
		$options = array(),
		$controller = null
	) {
		parent::__construct($array, $class, $options, $controller);
		$this->_extra = $extra;
	}

	/**
	 * @access protected
	 * @param Mixed $value
	 * @return Presenter
	 */
	protected function wrap($value) {
		$presenter = parent::wrap($this->_extra);
		$presenter->setContext($value);
		return $presenter;
	}
}
