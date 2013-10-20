<?php

App::uses('Component', 'Controller');
App::uses('Presenter', 'CakePHP-GiftWrap.Presenter');
App::uses('PresenterListIterator', 'CakePHP-GiftWrap.Lib');

class PresenterComponent extends Component {
	protected $_controller;
	protected $_viewVar = 'presenter';
	protected $_uses = null;
	protected $_data = array();
	protected $_options = array();

	public function __construct(ComponentCollection $collection, $settings = array()) {
		parent::__construct($collection, $settings);
		foreach (array('viewVar', 'options') as $key) {
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
			$this->_controller->set($this->_viewVar, $this->create());
		}
	}

	public function create($name = null) {
		$class = $this->getPresenterClass($name);
		return $this->newPresenter($class, $this->_data, $this->_options);
	}

	public function uses($name) {
		$this->_uses = $this->convertName($name);
	}

	public function viewVar($name) {
		$this->_viewVar = $name;
	}

	public function set($key, $value = null) {
		if (is_array($key)) {
			$this->_data = $key + $this->_data;
		} else {
			$this->_data[$key] = $value;
		}
	}

	public function setPresenter($key, $data, $name, $opts = array()) {
		$class = $this->getPresenterClass($name);
		$this->set($key, $this->newPresenter($class, $data, $opts));
	}

	public function setEachPresenter($key, $data, $name, $opts = array()) {
		$class = $this->getPresenterClass($name);
		$iter = new PresenterListIterator($data, $class, $opts);
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
				'Presenter'
			);
		}
		return $attempts;
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
