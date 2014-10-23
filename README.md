dispatcher.plugin.performer
=============================

A service performance plugin for comodojo/dispatcher.framework.

If enabled, this plugin will inject 4 different headers in a service response:

- D-Request-Sec: time spent by framework to model request
- D-Route-Sec: routing time
- D-Result-Sec: service execution time
- D-Total-Sec: total time, from request to response

Values are calculated in microseconds.

## Installation

Just require lib in a dispatcher.project installation (current version: 1.0.0):

	composer require comodojo/dispatcher.plugin.performer 1.0.*

## Usage

To enable performer in a single service, add `'perform' => true` into service parameters (routing-config.php), like:

```php

	$dispatcher->setRoute( "test_performance", "ROUTE", "performed.php", array ( 'perform' => true ) );

	```

To enable performer for all services, add `DISPATCHER_PERFORM_EVERYTHING` constant into dispatcher-config.php file:

```php

	define('DISPATCHER_PERFORM_EVERYTHING', true);

	```

## Demo

[Dispatcher test environment](https://github.com/comodojo/dispatcher.servicebundle.test) contains a "Dispatcher performance" case.

Test environment is also available on the [online demo](http://demo.comodojo.org/dispatcher/).