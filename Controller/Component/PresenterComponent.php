<?php

App::uses('Component', 'Controller');
App::uses('Presenter', 'CakePHP-GiftWrap.Presenter');
App::uses('PresenterListIterator', 'CakePHP-GiftWrap.Lib');

class PresenterComponent extends Component {
	protected $_controller;
	protected $_viewVar = 'presenter';
	protected $_uses = null;
	protected $_defaultClass = 'Presenter';
	protected $_data = array();
	protected $_options = array();

	public function __construct(ComponentCollection $collection, $settings = array()) {
		parent::__construct($collection, $settings);
		$this->findDefaultClass();
		foreach (array('viewVar', 'options', 'defaultClass') as $key) {
			if (array_key_exists($key, $settings)) {
				$var = '_'.$key;
				$this->{$var} = $settings[$key];
			}
		}
	}

	public function startup(Controller $controller) {
		$this->_controller = $controller;
	}

	public function beforeRender(Controller $controller) {
		if (!isset($this->_controller->viewVars[$this->_viewVar])) {
			$this->_controller->set($this->_viewVar, $this->getDefaultPresenter());
		}
	}

	public function getDefaultPresenter($name = null) {
		$class = $this->getPresenterClass($name);
		return $this->newPresenter($class, $this->_data, $this->_options);
	}

	public function create($class, $data = array(), $options = array()) {
		return $this->newPresenter($this->convertName($class), $data, $options);
	}

	public function uses($name) {
		$this->_uses = $this->convertName($name);
	}

	public function viewVar($name) {
		$this->_viewVar = $name;
	}

	public function defaultClass($name) {
		$this->_defaultClass = $name;
	}

	public function options($options = array()) {
		$this->_options = $options;
	}

	public function set($key, $value = null) {
		if (is_array($key)) {
			$this->_data = $key + $this->_data;
		} else {
			$this->_data[$key] = $value;
		}
	}

	public function setPresenter($key, $context, $name, $data = array(), $opts = array()) {
		$class = $this->getPresenterClass($name);
		$options = array('checkRequiredProperties' => false) + $opts;
		$presenter = $this->newPresenter($class, $data, $options);
		$presenter->setContext($context);
		$presenter->checkRequiredProperties();
		$this->set($key, $presenter);
	}

	public function setEachPresenter($key, $context, $name, $data = array(), $opts = array()) {
		$class = $this->getPresenterClass($name);
		$iter = new PresenterListIterator($context, $class, $data, $opts);
		$this->set($key, $iter);
	}

	protected function newPresenter($class, $data, $options) {
		return new $class($data, $options, $this->_controller);
	}

	protected function getPresenterClass($name = null) {
		$attempts = $this->getClassAttemptNames($name);
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
		if (!$presenter) {
			$class = array_pop($attempts);
			throw new LogicException(
				"Could not find presenter. Create $class in APP/Presenter/$class.php"
			);
		}
		return $presenter;
	}

	protected function getClassAttemptNames($name) {
		if (is_string($name)) {
			$attempts = array($this->convertName($name));
		} else if (is_string($this->_uses)) {
			$attempts = array($this->convertName($this->_uses));
		} else {
			$attempts = array(
				$this->controllerActionName(),
				$this->controllerName(),
				$this->_defaultClass
			);
		}
		return $attempts;
	}

	protected function findDefaultClass() {
		$name = 'AppPresenter';
		App::uses($name, 'Presenter');
		if (class_exists($name)) {
			$this->_defaultClass = $name;
		}
	}

	protected function convertName($name) {
		$camel = Inflector::camelize($name);
		return preg_replace('/Presenter$/', '', $camel).'Presenter';
	}

	protected function controllerActionName() {
		$name = $this->_controller->name . '_' . $this->_controller->action;
		return $this->convertName($name);
	}

	protected function controllerName() {
		return $this->convertName($this->_controller->name);
	}
}
