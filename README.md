# CakePHP-GiftWrap

## Requirements

* CakePHP >= 2.1
* PHP 5.3+

## Installation

```
git clone git://github.com:loadsys/CakePHP-GiftWrap.git Plugin/CakePHP-GiftWrap
echo "CakePlugin::load('CakePHP-GiftWrap', array('bootstrap' => true));" >> Config/bootstrap.php
```

## Usage

### Controller usage

Add the component to the list of components in the controller property.

```
// Controller/PostsController.php
class PostsController extends AppController {
  public $components = array(
    'CakePHP-GiftWrap.Presenter' => array(
      'viewVar' => 'object', // Defaults to 'presenter'
      'options' => array( // Passed the the creation of the default presenter
        'contextKey' => 'context' // Defaults to 'model'
      )
    )
  );
  ...
}
```

A presenter will be created for in beforeRender automatically. If you don't explicitly set the name of the presenter class to use with `PresenterComponent::uses($name)`, then one will be searched for by some conventional names. First, it looks for a class called `ControllerNameActionNamePresenter` (e.g. `PostsIndexPresenter`). Then, it will look for a class called `ControllerNamePresenter` (e.g. `PostsPresenter`). Finally, if those don't exist, then an instance of the base `Presenter` class is created.

In controller actions, you'll set data to the presenter instead of to the view. Since the eventually created presenter will be set to the view for you, you'll have access to all that data in the `$presenter` variable.

`PresenterComponent::set()` works the same as `Controller::set()` in that it can take 2 arguments (a key and value), or an array of keys => values.

`PresenterComponent::setPresenter()` sets a key that where the value is a presenter wrapping the data given to it.

`PresenterComponent::setEachPresenter()` sets a key on the presenter where the value is an array of things, and each element of the array will be a presenter instance.

`PresenterComponent::uses()` explicitly defines the name of the presenter class to create.

```
public function index() {
  $posts = $this->Post->find('all');
  $this->Presenter->set('names', array('Jim', 'Sally', 'Bob'));
  $this->Presenter->setEachPresenter('posts', $posts, 'PostPresenter');
}
public function view($id) {
  $model = $this->Post->read($id);
  $user = $this->currentUser();
  $this->Presenter->uses('PostPresenter');
  $this->Presenter->setPresenter('user', $user, 'UserPresenter');
  $this->Presenter->set(compact('model'));
}
```

In the `PresenterComponent::beforeRender()`, the presenter object is created and set to the view in the `$presenter` variable.

### Creating a Presenter

The component will look for presenters in `APP/Presenter`. These classes should inherit from
`Presenter`:

```
// APP/Presenter/PostPresenter.php

App::uses('Presenter', 'CakePHP-GiftWrap.Presenter');

class PostPresenter extends Presenter {
}
```

Here you can define view specific logic and use the values set to the presenter.

