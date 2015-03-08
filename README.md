# The Authentication Package [![Build Status](https://travis-ci.org/joomla-framework/authentication.png?branch=master)](https://travis-ci.org/joomla-framework/authentication) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/joomla-framework/authentication/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/joomla-framework/authentication/?branch=master)

The authentication package provides a simple interface to authenticate users in a Joomla Framework application. It is completely decoupled from the application class and provides the ability to implement custom authentication strategies.


## Installation via Composer

Add `"joomla/authentication": "~1.0"` to the require block in your composer.json and then run `composer install`.

```json
{
	"require": {
		"joomla/authentication": "~1.0"
	}
}
```

Alternatively, you can simply run the following from the command line:

```sh
composer require joomla/authentication "~1.0"
```
