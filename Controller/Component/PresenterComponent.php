<?php

App::uses('Component', 'Controller');
App::uses('PresenterFactory', 'GiftWrap.Lib');
App::uses('Presenter', 'GiftWrap.Presenter');
App::uses('PresenterNaming', 'GiftWrap.Lib');
App::uses('PresenterListIterator', 'GiftWrap.Lib');
App::uses('DecoratorListIterator', 'GiftWrap.Lib');

/**
 * User interface for using presenters.
 *
 * @author Joey Trapp <joey@loadsys.com>
 * @copyright Loadsys Web Strategies 2013
 * @version 1.0.0
 */
class PresenterComponent extends Component {
	/**
	 * @access protected
	 * @var Object $_controller
	 */
	protected $_controller;

	/**
	 * @access protected
	 * @var String $_viewVar
	 */
	protected $_viewVar = 'presenter';

	/**
	 * @access protected
	 * @var String $_viewVar
	 */
	protected $_uses = null;

	/**
	 * @access protected
	 * @var String $_defaultClass
	 */
	protected $_defaultClass = 'Presenter';

	/**
	 * @access protected
	 * @var Array $_data
	 */
	protected $_data = array();

	/**
	 * @access protected
	 * @var Array $_options
	 */
	protected $_options = array();

	/**
	 * @access protected
	 * @var Object $_defaultPresenter
	 */
	protected $_defaultPresenter = null;

	/**
	 * @access protected
	 * @var String $_baseLookupName
	 */
	protected $_baseLookupName = 'AppPresenter';

	/**
	 * For the supplied settings, set the local properties appropriately, and also
	 * check for the existance of an AppPresenter. If one exists, set it as the
	 * default fallback presenter that will be used instead of the base Presenter.
	 *
	 * @access public
	 * @param ComponentCollection $collection
	 * @param Array $settings
	 */
	public function __construct(
		ComponentCollection $collection,
		$settings = array()
	) {
		parent::__construct($collection, $settings);
		$this->setDefaultPresenterClass();
		$this->setOptions($settings);
	}

	/**
	 * Component startup callback. Used to get a local reference to the
	 * controller.
	 *
	 * @access public
	 * @param Controller $controller
	 */
	public function startup(Controller $controller) {
		$this->_controller = $controller;
	}

	/**
	 * Component beforeRender callback. Used to create the default presenter
	 * and set it to the view.
	 *
	 * @access public
	 * @param Controller $controller
	 */
	public function beforeRender(Controller $controller) {
		if (!isset($this->_controller->viewVars[$this->_viewVar])) {
			$this->setDefaultPresenterToController();
		}
	}

	/**
	 * For a supplied presenter class name (or null for the default presenter)
	 * this creates a presenter instance and sets it to the _defaultPresenter
	 * property. This presenter is alo returned.
	 *
	 * @access public
	 * @param String $name Name of presenter class to use or null to use default
	 * @return Presenter
	 */
	public function getDefaultPresenter($name = null) {
		$presenter = $this->create($name, $this->_data, $this->_options);
		return $this->_defaultPresenter = $presenter;
	}

	/**
	 * Create a new presenter object for a given class string, which is
	 * normalized to a presenter class name, with the supplied data and options
	 *
	 * @access public
	 * @param String $name
	 * @param Array $data
	 * @param Array $options
	 * @return Presenter
	 */
	public function create($name, $data = array(), $options = array()) {
		$class = $this->getPresenterClass($name);
		return new $class($data, $options, $this->_controller);
	}

	/**
	 * Pass a string class name to this method to define the Presenter class that
	 * will be created automatically and set to the view. If no param is passed,
	 * then the properties current value is returned.
	 *
	 * @access public
	 * @param String $name
	 */
	public function uses($name = null) {
		if ($name) {
			$this->_uses = $name;
			if ($this->_defaultPresenter) {
				$this->setDefaultPresenterToController();
			}
		} else {
			return $this->_uses;
		}
	}

	/**
	 * Pass a string to this method to change the variable that the default
	 * presenter is set to in the view. It defaults to `presenter`. If no
	 * param is passed, then the properties current value is returned.
	 *
	 * @access public
	 * @param String $name
	 */
	public function viewVar($name = null) {
		if ($name) {
			$this->_viewVar = $name;
		} else {
			return $this->_viewVar;
		}
	}

	/**
	 * Pass a string to this method to change the default fallback presenter class
	 * that will be used if the conventionally named classes don't exist. If no
	 * param is passed, then the properties current value is returned.
	 *
	 * @access public
	 * @param String $name
	 */
	public function defaultClass($name = null) {
		if ($name) {
			$this->_defaultClass = $name;
		} else {
			return $this->_defaultClass;
		}
	}

	/**
	 * The options that get passed to the default presenter can be set by passing
	 * an array to this method. If no param is passed, then the properties current
	 * value is returned.
	 *
	 * @access public
	 * @param Array $options
	 */
	public function options($options = array()) {
		if ($options) {
			$this->_options = $options;
		} else {
			return $this->_options;
		}
	}

	/**
	 * Sets data that will be used to create the default presenter. Set can take
	 * a string key and the data for that key in the second param, or an array
	 * key value pairs.
	 *
	 * @access public
	 * @param Mixed $key
	 * @param Mixed $value
	 */
	public function set($key, $value = null) {
		$data = is_array($key) ? $key : array($key => $value);
		$this->_data = $data + $this->_data;
		$this->setOnExistingPresenter($data);
	}

	/**
	 * Set an instance of another presenter to a key on the default presenter.
	 * This other presenter must take its own data and options, and will not
	 * use the data that has been set for the default presenter using
	 * PresenterComponent::set()
	 *
	 * @access public
	 * @param String $key
	 * @param String $class
	 * @param Mixed $data
	 * @param Array $options
	 */
	public function setPresenter(
		$key,
		$class,
		$data = array(),
		$options = array()
	) {
		$this->set($key, $this->create($class, $data, $options));
	}

	/**
	 * Set an instance of PresenterListIterator to a key on the default
	 * presenter. The array of data must be passed in, as the data set using
	 * PresenterComponent::set() will not be used.
	 *
	 * @access public
	 * @param String $key
	 * @param String $class
	 * @param Array $array
	 * @param Array $options
	 */
	public function setEachPresenter(
		$key,
		$class,
		$array = array(),
		$options = array()
	) {
		$class = $this->getPresenterClass($class);
		$this->set($key, new PresenterListIterator($array, $class, $options));
	}

	/**
	 * Sets an instance of another presenter to a key on the default presenter,
	 * but the context data passed to this method will be set to the context
	 * property of the presenter (making it act like a decorator). Additional
	 * data can be optionally passed in the 4th parameter.
	 *
	 * @access public
	 * @param String $key
	 * @param String $class
	 * @param Mixed $context
	 * @param Array $data
	 * @param Array $options
	 */
	public function setDecorator(
		$key,
		$class,
		$context = array(),
		$data = array(),
		$options = array()
	) {
		$presenter = $this->create($class, $data, $options);
		$presenter->setContext($context);
		$this->set($key, $presenter);
	}

	/**
	 * Set an instance of the DecoratorListIterator to a key on the default
	 * presenter. Each element of the array will be set to the context property
	 * of an instance of the supplied presenter class. Any additional data can
	 * be passed in the 4th parameter and will be present in all instances.
	 *
	 * @access public
	 * @param String $key
	 * @param String $class
	 * @param Array $contexts
	 * @param Array $data
	 * @param Array $options
	 */
	public function setEachDecorator(
		$key,
		$class,
		$contexts = array(),
		$data = array(),
		$options = array()
	) {
		$class = $this->getPresenterClass($class);
		$iter = new DecoratorListIterator($contexts, $class, $data, $options);
		$this->set($key, $iter);
	}

	/**
	 * @access protected
	 */
	protected function setDefaultPresenterToController() {
		if ($this->_controller instanceof Controller) {
			$this->_controller->set($this->_viewVar, $this->getDefaultPresenter());
		}
	}

	/**
	 * @access protected
	 * @param Array $data
	 * @return Class
	 */
	protected function getPresenterClass($name = null) {
		$names = new PresenterNaming($this->_controller, $this->_defaultClass);
		$name = $name ? $name : $this->_uses;
		return $names->getClass($name);
	}

	/**
	 * @access protected
	 * @param Array $settings
	 */
	protected function setOptions($settings = array()) {
		foreach (array('viewVar', 'options', 'defaultClass') as $key) {
			if (array_key_exists($key, $settings)) {
				$var = '_'.$key;
				$this->{$var} = $settings[$key];
			}
		}
	}

	/**
	 * @access protected
	 */
	protected function setDefaultPresenterClass() {
		App::uses($this->_baseLookupName, 'Presenter');
		if (class_exists($this->_baseLookupName)) {
			$this->_defaultClass = $this->_baseLookupName;
		}
	}

	/**
	 * @access protected
	 * @param Array $data
	 */
	protected function setOnExistingPresenter($data = array()) {
		if (isset($this->_controller->viewVars[$this->_viewVar])) {
			foreach ($data as $key => $value) {
				$this->_controller->viewVars[$this->_viewVar]->{$key} = $value;
			}
		}
	}
}
