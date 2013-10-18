<?php

App::uses('Component', 'Controller');
App::uses('Presenter', 'CakePHP-GiftWrap.Presenter');
App::uses('PresenterListIterator', 'CakePHP-GiftWrap.Lib');

class PresenterComponent extends Component {
	private $_controller;
	private $_uses = null;
	private $_data = array();
	private $_options = array();

	public function startup(Controller $controller) {
		$this->_controller = $controller;
	}

	public function create($name = null) {
		$presenter = $this->getPresenterClass($name);
		if (!$presenter) {
			// TODO: Describe what class to create and where.
			throw new LogicException("Could not find presenter.");
		}
		return new $presenter($this->_data, $this->_options);
	}

	public function uses($name) {
		$this->_uses = $this->convertName($name);
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
		$this->set($key, new $class($data, $opts));
	}

	public function setEachPresenter($key, $data, $name, $opts = array()) {
		$class = $this->getPresenterClass($name);
		$iter = new PresenterListIterator($data, $class, $opts);
		$this->set($key, $iter);
	}

	private function getPresenterClass($name = null) {
		$attempts = $this->getClassAttemptNames($name);
		$presenter = false;
		while (!empty($attempts)) {
			$class = array_shift($attempts);
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

	private function getClassAttemptNames($name) {
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

	private function convertName($name) {
		$camel = Inflector::camelize($name);
		return preg_replace('/Presenter$/', '', $camel).'Presenter';
	}

	private function controllerActionName() {
		$name = $this->_controller->name . '_' . $this->_controller->action;
		return $this->convertName($name);
	}

	private function controllerName() {
		return $this->convertName($this->_controller->name);
	}
}
