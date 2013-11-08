<?php

App::uses('Component', 'Controller');
App::uses('Presenter', 'CakePHP-GiftWrap.Presenter');
App::uses('PresenterNaming', 'CakePHP-GiftWrap.Lib');
App::uses('PresenterListIterator', 'CakePHP-GiftWrap.Lib');

class PresenterComponent extends Component {
	protected $_controller;
	protected $_viewVar = 'presenter';
	protected $_uses = null;
	protected $_defaultClass = 'Presenter';
	protected $_data = array();
	protected $_options = array();
	protected $_defaultPresenter = null;
	protected $_baseLookupName = 'AppPresenter';

	public function __construct(ComponentCollection $collection, $settings = array()) {
		parent::__construct($collection, $settings);
		$name = $this->_baseLookupName;
		App::uses($name, 'Presenter');
		if (class_exists($name)) { $this->_defaultClass = $name; }
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
		return $this->_defaultPresenter = $this->create($name, $this->_data, $this->_options);
	}

	public function create($name, $data = array(), $options = array()) {
		$class = $this->getPresenterClass($name);
		return new $class($data, $options, $this->_controller);
	}

	public function uses($name) {
		$this->_uses = $name;
		if ($this->_defaultPresenter) {
			$this->getDefaultPresenter();
		}
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
		$data = !is_array($key) ? array($key => $value) : $key;
		$this->_data = $data + $this->_data;
		$this->setToDefaultPresenter($data);
	}

	public function setPresenter($key, $context, $name, $data = array(), $opts = array()) {
		$presenter = $this->create($name, $data, $opts);
		$presenter->setContext($context);
		$this->set($key, $presenter);
	}

	public function setEachPresenter($key, $context, $name, $data = array(), $opts = array()) {
		$class = $this->getPresenterClass($name);
		$iter = new PresenterListIterator($context, $class, $data, $opts);
		$this->set($key, $iter);
	}

	protected function getPresenterClass($name = null) {
		$names = new PresenterNaming($this->_controller, $this->_defaultClass);
		$name = $name ? $name : $this->_uses;
		return $names->getClass($name);
	}

	protected function setToDefaultPresenter($data = array()) {
		if (isset($this->_controller->viewVars[$this->_viewVar])) {
			foreach ($data as $k => $v) {
				$this->_controller->viewVars[$this->_viewVar]->{$key} = $value;
			}
		}
	}
}
