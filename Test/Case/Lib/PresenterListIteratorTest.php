<?php

App::uses('Presenter', 'CakePHP-GiftWrap.Presenter');
App::uses('PresenterListIterator', 'CakePHP-GiftWrap.Lib');

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
}
