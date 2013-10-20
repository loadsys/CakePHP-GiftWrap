<?php

App::uses('Presenter', 'CakePHP-GiftWrap.Presenter');
App::uses('PresenterListIterator', 'CakePHP-GiftWrap.Lib');

class IteratorRequiredPresenter extends Presenter {
	public $requireProperties = array('model', 'one');
}

class PresenterListIteratorTest extends CakeTestCase {
	public function testWrapsEachItemInArrayInAPresenter() {
		$data = array('one', 'two', 'three');
		$list = new PresenterListIterator($data, 'Presenter');
		$this->assertEquals('one', $list[0]->model);
		$this->assertEquals('two', $list[1]->model);
		$this->assertEquals('three', $list[2]->model);
	}

	public function testPresenterObjectsAreCachedForReuse() {
		$data = array('one');
		$list = new PresenterListIterator($data, 'Presenter');
		$one = $list[0];
		$one->key = 'value';
		$this->assertEquals('value', $list[0]->key);
	}

	public function testPresenterSkipsRequireCheckUntilAfterContextIsSet() {
		try {
			$extra = array('one' => 'one');
			$data = array('context');
			$list = new PresenterListIterator($data, 'IteratorRequiredPresenter', $extra);
			$list[0]; // Must access property to make iterator create the presenter
		} catch (Exception $e) {
			$this->assertTrue(false, 'The created presenter threw a required property exception');
		}
		$this->assertEquals('context', $list[0]->model);
	}

	public function testPresenterStillFailsOnRequireCheckAfterContextIsSet() {
		try {
			$extra = array();
			$data = array('context');
			$list = new PresenterListIterator($data, 'IteratorRequiredPresenter', $extra);
			$list[0]; // Must access property to make iterator create the presenter
		} catch (Exception $e) {
			$this->assertRegExp('/: one/', $e->getMessage());
			return;
		}
		$this->assertTrue(false, 'The created presenter did not throw a required property exception');
	}
}
