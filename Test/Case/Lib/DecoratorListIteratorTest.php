<?php

App::uses('Presenter', 'GiftWrap.Presenter');
App::uses('DecoratorListIterator', 'GiftWrap.Lib');

class DecoratorListIteratorTest extends CakeTestCase {
	public function testWrapsEachItemInArrayInAPresenter() {
		$data = array('one', 'two', 'three');
		$list = new DecoratorListIterator($data, 'Presenter');
		$this->assertEquals('one', $list[0]->model);
		$this->assertEquals('two', $list[1]->model);
		$this->assertEquals('three', $list[2]->model);
	}

	public function testPresenterObjectsAreCachedForReuse() {
		$data = array('one');
		$list = new DecoratorListIterator($data, 'Presenter');
		$one = $list[0];
		$one->key = 'value';
		$this->assertEquals('value', $list[0]->key);
	}
}
