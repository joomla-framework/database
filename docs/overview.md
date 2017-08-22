## Overview

The Console package provides the infrastructure to build and run command line applications within the Joomla! Framework.

### Methodology

The Console package is built very closely modeling the [Symfony Console component](https://symfony.com/components/Console). Symfony provides an
excellent infrastructure however its two main pieces, the base Application class and the base Command class, are largely incompatible with
the Joomla code structure and methodology. Therefore, this package provides a replacement of sorts for these core classes and aims to be able
to use the Symfony component API as practical.

### Console Application

Unlike the Application's `AbstractCliApplication` class, this package provides a fully functional and somewhat opinionated `Application` class
which can be used to run `CommandInterface` implementations. In fact, your application can be bootstrapped with only a few lines of code.

```php
<?php
use Joomla\Console\Application;

$application = new Application;

// Register commands via $application->addCommand();

$application->execute();
```

### Console Commands

The `CommandInterface` defines the base API for console command classes, largely based on `Symfony\Component\Console\Command\Command`. To help
developers get started, we provide a `AbstractCommand` for building command classes.

When using the `AbstractCommand`, generally two methods are required in your classes to have a functional command:

- `initialise` is a hook for configuring the command class, conceptually this is similar to `Symfony\Component\Console\Command\Command::configure()`
- `execute` is the method which runs the command's logic

### Lazy Loading Commands

As of Symfony 3.4, Symfony supports lazy loading command classes through a command loader. Our Console package provides a similar feature that can
be registered to the application. Note that this is functionally equivalent to the Symfony component, however since its interface requires returning
`Symfony\Component\Console\Command\Command` objects we have elected to create our own implementation.


```php
<?php
use Joomla\Console\Application;
use Joomla\Console\Loader\ContainerLoader;
use Joomla\DI\Container;

// Note that this can be any PSR-11 container
$container = new Container;
$container->set('foo.command', new FooCommand);

// The mapping array is an associative array where the keys are the command names and the values are the container service IDs
$mapping = ['foo' => 'foo.command'];

$loader = new ContainerLoader($container, $mapping);

$application = new Application;
$application->setCommandLoader($loader);

// Register non-lazy commands via $application->addCommand();

$application->execute();
```
