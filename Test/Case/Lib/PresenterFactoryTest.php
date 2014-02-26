<?php

App::uses('PresenterFactory', 'GiftWrap.Lib');

class PresenterFactoryTest extends CakeTestCase {

	public function testCanGetAndSetDefaultClass() {
		$this->assertEquals('Presenter', PresenterFactory::defaultClass());
		PresenterFactory::defaultClass('AppPresenter');
		$this->assertEquals('AppPresenter', PresenterFactory::defaultClass());
	}
}
