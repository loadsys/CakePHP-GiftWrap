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

Coming soon.


## API

### Component API

**PresenterComponent::uses($className)**

$className *String* Class name with or without the Presenter part.

Set the class that will be used for the default presenter.

**PresenterComponent::viewVar($varName)**

$varName *String* Variable name that the default presenter will be set to.

Set the name of the variable the defautl presenter will be set into.

**PresenterComponent::defaultClass($className)**

$className *String* Class name with or without the Presenter part.

Set the class that will be used as a fallback if the conventionally named presenter classes don't exist.

**PresenterComponent::options($options)**

$options *Array* 

* contextKey *String* Which property of the Presenter will be the context. Default is `model`.

Set the options array that will be passed to the creation of the default presenter.

**PresenterComponent::create($className, $data, $options)**

$className *String* Class name with or without the Presenter part.
$data *Mixed* Data passed to the creation of the presenter.
$options *Array* Options passed to the creation of the presenter.

Creates a new instance of the supplied presenter class name with the given data and options.

**PresenterComponent::set($keyOrArray, $value)**

$keyOrArray *Mixed* Either a string key or an array of key/value pairs.
$value *Mixed* Value set when first param is a string key.

Set data for the default presenter by either passing a key and value, or an array of key/value pairs.

**PresenterComponent::setPresenter($key, $className, $data, $options)**

$key *String* Property on default presenter to set object to.
$className *String* Class name with or without the Presenter part.
$data *Array* Data for the new presenter instance.
$options *Array* Options for the new presenter instance.

Creates an instance of the supplied presenter class name with the data and options and sets it to the key on the default presenter.

**PresenterComponent::setEachPresenter($key, $className, $array, $options)**

$key *String* Property on default presenter to set object to.
$className *String* Class name with or without the Presenter part.
$array *Array* List of data, that each element is wrapped with a presenter.
$options *Array* Options for the new presenter instance.

Creates an iterator that wraps the supplied array. Each element of the array becomes an instance of the supplied presenter class name, where the data is the current element of the array and the options apply to all instances.

**PresenterComponent::setDecorator($key, $className, $context, $extra, $options)**

$key *String* Property on default presenter to set object to.
$className *String* Class name with or without the Presenter part.
$context *Mixed* Data that is set the the contextKey property on the presenter.
$extra *Array* Data for the new presenter instance.
$options *Array* Options for the new presenter instance.

Creates an instance of the supplied presenter class name. The `$context` variable is set to the context property of the presenter, and the `$extra` data is used when creating the presenter.

**PresenterComponent::setEachDecorator($key, $className, $contexts, $extra, $options)**

$key *String* Property on default presenter to set object to.
$className *String* Class name with or without the Presenter part.
$contexts *Array* List of data that is set to the presenters contextKey property.
$extra *Array* Data for the new presenter instance.
$options *Array* Options for the new presenter instance.

Creates an iterator that wraps the contexts. Each context becomes an instance of the supplied presenter class name. The presenter is created with the data supplied in `$extra`, and the context is set to the context property on the presenter instance.


## License

[MIT](https://github.com/loadsys/CakePHP-GiftWrap/blob/master/LICENSE.md)


## Copyright

[Loadsys Web Strategies](http://www.loadsys.com) 2013