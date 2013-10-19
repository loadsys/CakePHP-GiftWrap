<?php

App::uses('Presenter', 'CakePHP-GiftWrap.Presenter');

class TestView {
	public function viewMethod() { return 'view method'; }
}

class RequiredPresenter extends Presenter {
	public $requiredProperties = array('one', 'two');
}

class PresenterTest extends CakeTestCase {
	public function testPresenterAssignsKeysInArrayAsProperties() {
		$presenter = new Presenter(array('one' => 1, 'two' => 'TWO'));
		$this->assertEquals(1, $presenter->one);
		$this->assertEquals('TWO', $presenter->two);
	}

	public function testPresenterCanBeGivenANonArrayAndItWillBeSetToDefaultContent() {
		$presenter = new Presenter('something');
		$this->assertEquals('something', $presenter->model);
	}

	public function testPresenterSetContentSetsTheDefaultContentForPresenter() {
		$presenter = new Presenter(array('one' => 1), array('contextKey' => 'content'));
		$presenter->setContext(array('seven' => 7));
		$this->assertEquals(1, $presenter->one);
		$this->assertEquals(array('seven' => 7), $presenter->content);
		$this->assertEquals(7, $presenter['seven']);
	}

	public function testPresenterAllowsArrayAccessToProperies() {
		$presenter = new Presenter(array('three' => 3, 'four' => 'FOUR', 'six' => 6));
		$presenter['five'] = '5';
		unset($presenter['six']);
		$this->assertEquals(3, $presenter['three']);
		$this->assertEquals('FOUR', $presenter['four']);
		$this->assertEquals('5', $presenter['five']);
		$this->assertTrue(isset($presenter['three']));
		try {
			$presenter['six'];
		} catch (Exception $e) {
			return;
		}
		$this->assertTrue(false, 'Accessing the unset key did not throw an error');
	}

	public function testPresenterProxiesArrayAccessGetToContentWhenPropertyMissing() {
		$presenter = new Presenter(array('model' => array('one' => 1), 'two' => 2));
		$this->assertEquals(1, $presenter['one']);
		$this->assertEquals(2, $presenter['two']);
	}

	public function testPresenterCanHaveProxyPropertyConfigured() {
		$data = array('proxy' => array('three' => 3), 'four' => 4);
		$presenter = new Presenter($data, array('contextKey' => 'proxy'));
		$this->assertEquals(3, $presenter['three']);
		$this->assertEquals(4, $presenter['four']);
	}

	public function testPresenterProxiesUndefinedMethodsToView() {
		$mock = $this->getMock('stdClass');
		$mock->View = new TestView;
		$presenter = new Presenter(array(), array(), $mock);
		$this->assertEquals('view method', $presenter->viewMethod());
	}

	public function testPresenterLooksForPropertyInViewHelpersIfNotDefined() {
		$mock = $this->getMock('stdClass');
		$mock->View = new TestView;
		$mock->View->Helpers = new stdClass;
		$mock->View->Helpers->Html = 'Html Helper';
		$presenter = new Presenter(array(), array(), $mock);
		$this->assertEquals('Html Helper', $presenter->Html);
	}

	public function testPresenterThrowsErrorOnCreationWhenNotCreatedWithRequiredProps() {
		try {
			$presenter = new RequiredPresenter(array('one' => 1));
		} catch (Exception $e) {
			$this->assertEquals('RequiredPresenter missing properties: two', $e->getMessage());
			return;
		}
		$this->assertTrue(false, 'Exception not thrown for requiredProperties');
	}

	public function testPresenterDoesNotErrorWhenGivenMoreThanRequiredProps() {
		$presenter = new RequiredPresenter(array('one' => 1, 'two' => 2, 'three' => 3));
		$this->assertTrue(true, 'Exception not thrown for requiredProperties');
	}
}
