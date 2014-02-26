<?php

App::uses('PresenterNaming', 'GiftWrap.Lib');
App::uses('Presenter', 'GiftWrap.Presenter');

class NamingTestPresenter extends Presenter {
}

class NamingTestViewPresenter extends Presenter {
}

class NamingTestSpecialPresenter extends Presenter {
}

class PresenterNamingTest extends CakeTestCase {
	public function buildController($name, $action) {
		$obj = new stdClass;
		$obj->name = $name;
		$obj->action = $action;
		return $obj;
	}

	public function testConvertNamesMakesAPresenterClassNameOutOfGiventString() {
		$tests = array(
			'post'          => 'PostPresenter',
			'action_item'   => 'ActionItemPresenter',
			'Project'       => 'ProjectPresenter',
			'blogSummary'   => 'BlogSummaryPresenter',
			'NamePresenter' => 'NamePresenter'
		);
		foreach ($tests as $str => $class) {
			$this->assertEquals($class, PresenterNaming::classify($str));
		}
	}

	public function testGetClassReturnsClassNameForPresenterThatExists() {
		$controller = $this->buildController('Post', 'index');
		$naming = new PresenterNaming($controller);
		$this->assertEquals('Presenter', $naming->getClass());
	}

	public function testGetClassReturnsClassNameForControllerWhenThatExists() {
		$controller = $this->buildController('NamingTest', 'index');
		$naming = new PresenterNaming($controller);
		$this->assertEquals('NamingTestPresenter', $naming->getClass());
	}

	public function testGetClassReturnsClassNameForControllerActionWhenThatExists() {
		$controller = $this->buildController('NamingTest', 'view');
		$naming = new PresenterNaming($controller);
		$this->assertEquals('NamingTestViewPresenter', $naming->getClass());
	}

	public function testGetClassReturnsClassNameForPassedInClassName() {
		$controller = $this->buildController('NamingTest', 'view');
		$naming = new PresenterNaming($controller);
		$str = $naming->getClass('NamingTestSpecial');
		$this->assertEquals('NamingTestSpecialPresenter', $str);
	}

	public function testGetClassThrowsErrorWhenPassedAClassThatDoesNotExist() {
		$controller = $this->buildController('NamingTest', 'view');
		$naming = new PresenterNaming($controller);
		try {
			$naming->getClass('NamingTestMissing');
		} catch (Exception $e) {
			$this->assertRegExp('/Create NamingTestMissingPresenter/', $e->getMessage());
			return;
		}
		$this->assertTrue(false, "getClass didn't throw error for missing presenter");
	}
}
