<?php

App::uses('App', 'Core');
App::uses('Inflector', 'Utility');

class PresenterNaming {
	public static function classify($name) {
		$camel = Inflector::camelize($name);
		return preg_replace('/Presenter$/', '', $camel).'Presenter';
	}

	public function __construct($controller, $default = 'Presenter') {
		$this->controller = $controller;
		$this->default = $default;
	}

	public function getClass($name = null) {
		return $this->getPresenterClass($name);
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
			$attempts = array($this->classify($name));
		} else {
			$attempts = array(
				$this->controllerActionName(),
				$this->controllerName(),
				$this->default
			);
		}
		return $attempts;
	}

	protected function controllerActionName() {
		$name = $this->controller->name . '_' . $this->controller->action;
		return $this->classify($name);
	}

	protected function controllerName() {
		return $this->classify($this->controller->name);
	}
}
