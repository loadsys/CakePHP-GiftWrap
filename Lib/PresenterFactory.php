<?php

App::uses('Presenter', 'CakePHP-GiftWrap.Presenter');
App::uses('PresenterNaming', 'CakePHP-GiftWrap.Lib');
App::uses('PresenterListIterator', 'CakePHP-GiftWrap.Lib');
App::uses('DecoratorListIterator', 'CakePHP-GiftWrap.Lib');

class PresenterFactory {
	protected static $_defaultClass = 'Presenter';

	public static function defaultClass($name = null) {
		if ($name) {
			self::$_defaultClass = $name;
		} else {
			return self::$_defaultClass;
		}
	}
}
