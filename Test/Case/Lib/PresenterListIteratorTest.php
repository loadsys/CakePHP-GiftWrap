<?php

App::uses('Presenter', 'CakePHP-GiftWrap.Presenter');
App::uses('PresenterListIterator', 'CakePHP-GiftWrap.Lib');

class PresenterListIteratorTest extends CakeTestCase {
	public function testWrapsEachItemInArrayInAPresenter() {
		$data = array(array('one' => 1), array('two' => 2), array('three' => 3));
		$list = new PresenterListIterator($data, 'Presenter');
		$this->assertEquals(1, $list[0]->one);
		$this->assertEquals(2, $list[1]->two);
		$this->assertEquals(3, $list[2]->three);
	}

	public function testPresenterObjectsAreCachedForReuse() {
		$data = array(array('one' => 1));
		$list = new PresenterListIterator($data, 'Presenter');
		$one = $list[0];
		$one->key = 'value';
		$this->assertEquals('value', $list[0]->key);
	}
}
