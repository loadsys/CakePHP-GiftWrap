<?php

App::uses('Presenter', 'CakePHP-GiftWrap.Presenter');
App::uses('View', 'View');

class TestView {
	public function viewMethod() { return 'view method'; }
}

class PresenterTest extends CakeTestCase {
	public function testPresenterAssignsKeysInArrayAsProperties() {
		$presenter = new Presenter(array('one' => 1, 'two' => 'TWO'));
		$this->assertEquals(1, $presenter->one);
		$this->assertEquals('TWO', $presenter->two);
	}

	public function testPresenterOnlyAssignsNonNumericKeys() {
		$presenter = new Presenter(array('first' => 1, 'second'));
		$this->assertEquals(1, $presenter->first);
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

	public function testPresenterProxiesObjectGetAccessToContextWhenPropertyMissing() {
		$mock = new stdClass();
		$mock->modelProperty = 'model property';
		$presenter = new Presenter(array('model' => $mock), array());
		$this->assertEquals('model property', $presenter->modelProperty);
	}

	public function testPresenterCanHaveProxyPropertyConfigured() {
		$data = array('proxy' => array('three' => 3), 'four' => 4);
		$presenter = new Presenter($data, array('contextKey' => 'proxy'));
		$this->assertEquals(3, $presenter['three']);
		$this->assertEquals(4, $presenter['four']);
	}

	public function testPresenterProxiesUndefinedMethodsToContextIfItIsAnObject() {
		$mock = $this->getMock('stdClass', array('contextMethod'));
		$mock->expects($this->once())
		     ->method('contextMethod')
		     ->will($this->returnValue('context method'));
		$presenter = new Presenter(array('model' => $mock), array());
		$this->assertEquals('context method', $presenter->contextMethod());
	}

	public function testPresenterProxiesUndefinedMethodsToView() {
		$mock = $this->getMock('stdClass');
		$mock->View = new TestView;
		$presenter = new Presenter(array(), array(), $mock);
		$this->assertEquals('view method', $presenter->viewMethod());
	}

	public function testPresenterWarnsThatMethodDoesNotExistOnPresenterWhenNotPresentInView() {
		$mock = $this->getMock('stdClass');
		$mock->View = new TestView;
		$presenter = new Presenter(array(), array(), $mock);
		try {
			$presenter->undefinedMethod();
		} catch (Exception $e) {
			$this->assertRegExp('/Call to undefined method: undefinedMethod in/', $e->getMessage());
			return;
		}
		$this->assertTrue(false, 'Missing method error not thrown');
	}

	public function testPresenterLooksForPropertyInViewHelpersIfNotDefined() {
		$mock = $this->getMock('stdClass');
		$mock->View = $this->getMock('View');
		$mock->View->Helpers->Html = 'Html Helper';
		$presenter = new Presenter(array(), array(), $mock);
		$this->assertEquals('Html Helper', $presenter->Html);
	}

	public function testPresenterWarnsThatPropertyDoesNotExistOnPresenterWhenNotPresentInView() {
		$mock = $this->getMock('stdClass');
		$mock->View = $this->getMock('View');
		$presenter = new Presenter(array(), array(), $mock);
		try {
			$presenter->Time;
		} catch (Exception $e) {
			$this->assertRegExp('/Undefined property: Time in/', $e->getMessage());
			return;
		}
		$this->assertTrue(false, 'Missing property error not thrown');
	}
}
