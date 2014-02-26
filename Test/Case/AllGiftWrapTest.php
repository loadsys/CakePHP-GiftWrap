<?php
/**
 * All GiftWrap plugin tests
 */
class AllGiftWrapTest extends CakeTestCase {

/**
 * Suite define the tests for this plugin
 *
 * @return void
 */
	public static function suite() {
		$suite = new CakeTestSuite('All GiftWrap test');

		$path = CakePlugin::path('GiftWrap') . 'Test' . DS . 'Case' . DS;
		$suite->addTestDirectoryRecursive($path);

		return $suite;
	}

}
