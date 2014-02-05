<?php

App::uses('Controller', 'Controller');

class GiftWrapController extends Controller {
	public function set($key, $value = null) {
		parent::set($key, $value);
		if ($key !== $this->Presenter->viewVar()) {
			$this->Presenter->set($key, $value);
		}
	}
}
