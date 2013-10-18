<?php

App::uses('Presenter', 'GiftWrap.Presenter');

class PresenterTest extends CakeTestCase {
	public function testPresenterAssignsKeysInArrayAsProperties() {
		$presenter = new Presenter(array('one' => 1, 'two' => 'TWO'));
		$this->assertEquals(1, $presenter->one);
		$this->assertEquals('TWO', $presenter->two);
	}

	public function testPresenterCanBeGivenANonArrayAndItWillBeSetToDefaultContent() {
		$presenter = new Presenter('something');
		$this->assertEquals('something', $presenter->content);
	}

	public function testPresenterSetContentSetsTheDefaultContentForPresenter() {
		$presenter = new Presenter(array('one' => 1), array('content' => 'model'));
		$presenter->setContent(array('seven' => 7));
		$this->assertEquals(1, $presenter->one);
		$this->assertEquals(array('seven' => 7), $presenter->model);
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
		$presenter = new Presenter(array('content' => array('one' => 1), 'two' => 2));
		$this->assertEquals(1, $presenter['one']);
		$this->assertEquals(2, $presenter['two']);
	}

	public function testPresenterCanHaveProxyPropertyConfigured() {
		$data = array('proxy' => array('three' => 3), 'four' => 4);
		$presenter = new Presenter($data, array('content' => 'proxy'));
		$this->assertEquals(3, $presenter['three']);
		$this->assertEquals(4, $presenter['four']);
	}
}
