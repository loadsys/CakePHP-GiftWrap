<?php

App::uses('App', 'Core');
App::uses('Inflector', 'Utility');

/**
 * Encapsulates the conventional naming for Presenters. Instances of this class
 * can also attempt to load the class needed or throw an error with description
 * where to create the class file.
 *
 * @author Joey Trapp <joey@loadsys.com>
 * @copyright Loadsys Web Strategies 2013
 * @version 1.0.0
 */
class PresenterNaming {
	/**
	 * @access protected
	 * @var String $errMsg
	 */
	protected $errMsg = "Could not find presenter. Create %s in APP/Presenter/%s.php";

	/**
	 * Coverts a given string to a normalized Presenter class name.
	 *
	 * @static
	 * @access public
	 * @param String $name Name to be converted to a Presenter class name
	 * @return String
	 */
	public static function classify($name) {
		$camel = Inflector::camelize($name);
		return preg_replace('/Presenter$/', '', $camel).'Presenter';
	}

	/**
	 * Instances of this class need the controller for the request to be able to
	 * create the conventionally named classes to look up. The fallback class
	 * name can be passed in $default. When all other conventional classes are not
	 * found, this class will be used.
	 *
	 * @access public
	 * @param Controller $controller
	 * @param String $default
	 */
	public function __construct($controller, $default = 'Presenter') {
		$this->controller = $controller;
		$this->default = $default;
	}

	/**
	 * Finds a Presenter class and returns it. If not class name is supplied, then
	 * conventionally named classes are looked for and a default fallback class
	 * is used if those don't exist. If a class is given, assume that this class
	 * must exist and an exception is thrown when that class does not exist.
	 *
	 * @access public
	 * @param Mixed $class
	 * @throws LogicException
	 * @return Class
	 */
	public function getClass($class = null) {
		$attempts = $this->getClassAttemptNames($class);
		$presenter = $this->findClass($attempts);
		$this->raiseError($presenter, array_pop($attempts));
		return $presenter;
	}

	/**
	 * @access protected
	 * @param Array $attempts
	 * @return Class
	 */
	protected function findClass($attempts = array()) {
		$presenter = false;
		foreach ($attempts as $class) {
			if (!class_exists($class)) {
				App::uses($class, 'Presenter');
			}
			if (class_exists($class)) {
				$presenter = $class;
				break;
			}
		}
		return $presenter;
	}

	/**
	 * @access protected
	 * @param Mixed $presenter
	 * @param String $class
	 * @throws LogicException
	 */
	protected function raiseError($presenter, $class) {
		if (!$presenter) {
			throw new LogicException(sprintf($this->errMsg, $class, $class));
		}
	}

	/**
	 * @access protected
	 * @param Mixed $name
	 * @return Array
	 */
	protected function getClassAttemptNames($name = null) {
		return is_string($name) ? array($this->classify($name))
		                        : $this->getConventionalAttempts();;
	}

	/**
	 * @access protected
	 * @return Array
	 */
	protected function getConventionalAttempts() {
		return array(
			$this->controllerActionName(),
			$this->controllerName(),
			$this->default
		);
	}

	/**
	 * @access protected
	 * @return String
	 */
	protected function controllerActionName() {
		$name = $this->controller->name . '_' . $this->controller->action;
		return $this->classify($name);
	}

	/**
	 * @access protected
	 * @return String
	 */
	protected function controllerName() {
		return $this->classify($this->controller->name);
	}
}
