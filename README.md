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


## API

### Component API

#### PresenterComponent::uses($className)

Set the class that will be used for the default presenter.

#### PresenterComponent::viewVar($varName)

Set the name of the variable the defautl presenter will be set into.

#### PresenterComponent::defaultClass($className)

Set the class that will be used as a fallback if the conventionally named presenter classes don't exist.

#### PresenterComponent::options($options)

Set the options array that will be passed to the creation of the default presenter.

#### PresenterComponent::create($className, $data, $options)

Creates a new instance of the supplied presenter class name with the given data and options.

#### PresenterComponent::set($keyOrArray, $value)

Set data for the default presenter by either passing a key and value, or an array of key/value pairs.

#### PresenterComponent::setPresenter($key, $className, $data, $options)

Creates an instance of the supplied presenter class name with the data and options and sets it to the key on the default presenter.

#### PresenterComponent::setEachPresenter($key, $className, $array, $options)

Creates an iterator that wraps the supplied array. Each element of the array becomes an instance of the supplied presenter class name, where the data is the current element of the array and the options apply to all instances.

#### PresenterComponent::setDecorator($key, $className, $context, $extra, $options)

Creates an instance of the supplied presenter class name. The `$context` variable is set to the context property of the presenter, and the `$extra` data is used when creating the presenter.

#### PresenterComponent::setEachDecorator($key, $className, $contexts, $extra, $options)

Creates an iterator that wraps the contexts. Each context becomes an instance of the supplied presenter class name. The presenter is created with the data supplied in `$extra`, and the context is set to the context property on the presenter instance.


## License

[MIT](https://github.com/loadsys/CakePHP-GiftWrap/blob/master/LICENSE.md)


## Copyright

[Loadsys Web Strategies](http://www.loadsys.com) 2013