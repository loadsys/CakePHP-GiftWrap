<?php

App::uses('Presenter', 'CakePHP-GiftWrap.Presenter');
App::uses('PresenterListIterator', 'CakePHP-GiftWrap.Lib');

class PresenterListIteratorTest extends CakeTestCase {
	public function testWrapsEachItemInArrayInAPresenter() {
		$data = array('one', 'two', 'three');
		$list = new PresenterListIterator($data, 'Presenter');
		$this->assertEquals('one', $list[0]->content);
		$this->assertEquals('two', $list[1]->content);
		$this->assertEquals('three', $list[2]->content);
	}
}
