# The Authentication Package [![Build Status](https://ci.joomla.org/api/badges/joomla-framework/authentication/status.svg)](https://ci.joomla.org/joomla-framework/authentication)

[![Latest Stable Version](https://poser.pugx.org/joomla/authentication/v/stable)](https://packagist.org/packages/joomla/authentication)
[![Total Downloads](https://poser.pugx.org/joomla/authentication/downloads)](https://packagist.org/packages/joomla/authentication)
[![Latest Unstable Version](https://poser.pugx.org/joomla/authentication/v/unstable)](https://packagist.org/packages/joomla/authentication)
[![License](https://poser.pugx.org/joomla/authentication/license)](https://packagist.org/packages/joomla/authentication)

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

If you want to include the test sources and docs, use

```sh
composer require --prefer-source joomla/authentication "~1.0"
```
