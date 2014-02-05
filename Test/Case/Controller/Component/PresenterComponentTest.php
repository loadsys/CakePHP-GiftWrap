<?php

App::uses('Controller', 'Controller');
App::uses('PresenterComponent', 'CakePHP-GiftWrap.Controller/Component');
App::uses('Presenter', 'CakePHP-GiftWrap.Presenter');
App::uses('CakeRequest', 'Network');
App::uses('CakeResponse', 'Network');

class PresenterTestController extends Controller {
	public $name = 'PresenterTest';
	public $components = array('CakePHP-GiftWrap.Presenter');
}

class StrangeNamePresenter extends Presenter {
}

class PresenterTestNamePresenter extends Presenter {
}

class PresenterTestNameIndexPresenter extends Presenter {
}

class PresenterComponentTest extends CakeTestCase {
	public $Presenter;
	public $Controller;
	public $request;

	public function setUp() {
		parent::setUp();
		$ComponentCollection = $this->getMock('ComponentCollection');
		$this->request = new CakeRequest('presenter/index');
		$this->Controller = new PresenterTestController($this->request);
		$this->Presenter = new PresenterComponent($ComponentCollection, array());
		$this->Presenter->Controller = $this->Controller;
		$this->Presenter->startup($this->Controller);
	}

	public function tearDown() {
		parent::tearDown();
		unset($this->Presenter, $this->Controller, $this->request);
	}

	public function testCanCreateAPresenter() {
		$presenter = $this->Presenter->getDefaultPresenter();
		$this->assertInstanceOf('Presenter', $presenter);
	}

	public function testWilFindControllerActionPresenterFirst() {
		$this->Controller->name = 'PresenterTestName';
		$this->Controller->action = 'index';
		$presenter = $this->Presenter->getDefaultPresenter();
		$this->assertInstanceOf('PresenterTestNameIndexPresenter', $presenter);
	}

	public function testWillFindControllerPresenterWhenControllerActionNotFound() {
		$this->Controller->name = 'PresenterTestName';
		$this->Controller->action = 'view';
		$presenter = $this->Presenter->getDefaultPresenter();
		$this->assertInstanceOf('PresenterTestNamePresenter', $presenter);
	}

	public function testCanGetViewVarPropertyValue() {
		$this->assertEquals('presenter', $this->Presenter->viewVar());
	}

	public function testCanGetDefaultClassPropertyValue() {
		$this->assertEquals('Presenter', $this->Presenter->defaultClass());
	}

	public function testCanGetUsesPropertyValue() {
		$this->Presenter->uses('SetUses');
		$this->assertEquals('SetUses', $this->Presenter->uses());
	}

	public function testCanSetWhichPresenterToUse() {
		$this->Presenter->uses('StrangeName');
		$presenter = $this->Presenter->getDefaultPresenter();
		$this->assertInstanceOf('StrangeNamePresenter', $presenter);
	}

	public function testCanCreateAPresenterByName() {
		$presenter = $this->Presenter->create('StrangeName', array('first' => 'one'));
		$this->assertInstanceOf('StrangeNamePresenter', $presenter);
		$this->assertEquals('one', $presenter->first);
	}

	public function testCanSetDataToBeAddedToNewPresenterWithKeyAndValue() {
		$this->Presenter->set('one', 1);
		$presenter = $this->Presenter->getDefaultPresenter();
		$this->assertEquals(1, $presenter->one);
	}

	public function testCanSetDataToBeAddedToNewPresenterWithArray() {
		$this->Presenter->set(array('two' => 2));
		$presenter = $this->Presenter->getDefaultPresenter();
		$this->assertEquals(2, $presenter->two);
	}

	public function testCanSetTheSameKeyTwiceAndSecondSetWins() {
		$this->Presenter->set('three', 'THREE');
		$this->Presenter->set(array('three' => 3));
		$presenter = $this->Presenter->getDefaultPresenter();
		$this->assertEquals(3, $presenter->three);
	}

	public function testCanSetPropertyThatIsWrappedAsADecorator() {
		$context = 'context';
		$data = array('second' => 'second');
		$this->Presenter->setDecorator('test', 'PresenterTestName', $context, $data);
		$presenter = $this->Presenter->getDefaultPresenter();
		$this->assertEquals('context', $presenter->test->model);
		$this->assertEquals('second', $presenter->test->second);
	}

	public function testCanSetDecoratorForEachElementInASuppliedArray() {
		$data = array(array('first' => 1), array('second' => 2));
		$this->Presenter->setEachDecorator('list', 'PresenterTestName', $data);
		$presenter = $this->Presenter->getDefaultPresenter();
		$this->assertEquals(array('first' => 1), $presenter->list[0]->model);
		$this->assertEquals(array('second' => 2), $presenter->list[1]->model);
	}

	public function testCanSetPropertyThatIsWrappedAsAPresenter() {
		$data = array('second' => 'second');
		$this->Presenter->setPresenter('test', 'PresenterTestName', $data);
		$presenter = $this->Presenter->getDefaultPresenter();
		$this->assertEquals('second', $presenter->test->second);
	}

	public function testCanSetPresenterForEachElementInASuppliedArray() {
		$data = array(array('first' => 1), array('second' => 2));
		$this->Presenter->setEachPresenter('list', 'PresenterTestName', $data);
		$presenter = $this->Presenter->getDefaultPresenter();
		$this->assertEquals(1, $presenter->list[0]->first);
		$this->assertEquals(2, $presenter->list[1]->second);
	}

	public function testCreatedPresenterHasTheControllerForAccessToView() {
		$mock = $this->getMock('Controller');
		$mock->View = new stdClass;
		$mock->View->Helpers = new stdClass;
		$mock->View->Helpers->Form = 'Form Helper';
		$this->Controller = $mock;
		$this->Presenter->startup($this->Controller);
		$presenter = $this->Presenter->getDefaultPresenter();
		$this->assertEquals('Form Helper', $presenter->Form);
	}

	public function testSettingPresenterClassToUseThatDoesNotExistThrowsError() {
		try {
			$this->Presenter->uses('NotRealPresenter');
			$presenter = $this->Presenter->getDefaultPresenter();
		} catch (Exception $e) {
			$this->assertRegExp('/Create NotRealPresenter/', $e->getMessage());
			return;
		}
		$this->assertTrue(false, 'Creating missing presenter did not throw error');
	}

	public function testSettingDataAfterDefaultPresenterIsCreatedSetsProperly() {
		$this->Presenter->set('one', 1);
		$this->Presenter->beforeRender($this->Controller);
		$this->Presenter->set('two', 2);
		$presenter = $this->Controller->viewVars['presenter'];
		$this->assertEquals(1, $presenter->one);
		$this->assertEquals(2, $presenter->two);
	}
}
