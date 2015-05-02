# The Renderer Package [![Build Status](https://travis-ci.org/joomla-framework/renderer.svg?branch=master)](https://travis-ci.org/joomla-framework/renderer)

## Interfaces

### `Renderer\RendererInterface`

`Renderer\RendererInterface` is an interface to provide a common rendering API.

## Classes

* `Renderer\MustacheRenderer`
* `Renderer\PhpEngineRenderer`
* `Renderer\PlatesRenderer`
* `Renderer\TwigRenderer`

All classes except `PlatesRenderer` extend the parent rendering engine classes to enable those engines to implement the `RendererInterface`.

##### Usage

`Renderer\RendererInterface` classes can be instantiated in the same manner as their parent implementations, please refer to the vendor documentation for further details.

To assist with using these classes, example service providers are shared in this repository's [samples](/samples) folder, as well as the sample configuration which these providers are dependent upon.

###### Example Use Case

An example use of the `Renderer\RendererInterface` is provided here.  In this example, our controller class builds a view class based on `Joomla\View\ViewInterface` and injects the required dependencies.

Sample Controller:
```php
namespace Joomla\Controller;

use Joomla\Renderer\RendererInterface;

use Joomla\Controller\AbstractController;
use Joomla\DI\ContainerAwareInterface;
use Joomla\DI\ContainerAwareTrait;
use Joomla\View\ViewInterface;

/**
 * Default controller class for the application
 *
 * @since  1.0
 */
class DefaultController extends AbstractController implements ContainerAwareInterface
{
	use ContainerAwareTrait;

	/**
	 * The default view for the application
	 *
	 * @var    string
	 * @since  1.0
	 */
	protected $defaultView = 'dashboard';

	/**
	 * State object to inject into the model
	 *
	 * @var    \Joomla\Registry\Registry
	 * @since  1.0
	 */
	protected $modelState = null;

	/**
	 * Execute the controller
	 *
	 * This is a generic method to execute and render a view and is not suitable for tasks
	 *
	 * @return  boolean  True if controller finished execution
	 *
	 * @since   1.0
	 * @throws  \RuntimeException
	 */
	public function execute()
	{
		try
		{
			// Initialize the view object
			$view = $this->initializeView();

			// Render our view.
			$this->getApplication()->setBody($view->render());

			return true;
		}
		catch (\Exception $e)
		{
			throw new \RuntimeException(sprintf('Error: ' . $e->getMessage()), $e->getCode());
		}
	}

	/**
	 * Method to initialize the model object
	 *
	 * @return  \Joomla\Model\ModelInterface
	 *
	 * @since   1.0
	 * @throws  \RuntimeException
	 */
	protected function initializeModel()
	{
		$model = '\\Joomla\\Model\\' . ucfirst($this->getInput()->getWord('view')) . 'Model';

		// If a model doesn't exist for our view, revert to the default model
		if (!class_exists($model))
		{
			$model = '\\Joomla\\Model\\DefaultModel';

			// If there still isn't a class, panic.
			if (!class_exists($model))
			{
				throw new \RuntimeException(sprintf('No model found for view %s', $vName), 500);
			}
		}

		return new $model($this->modelState);
	}

	/**
	 * Method to initialize the renderer object
	 *
	 * @return  RendererInterface  Renderer object
	 *
	 * @since   1.0
	 * @throws  \RuntimeException
	 */
	protected function initializeRenderer()
	{
		$type = $this->getContainer()->get('config')->get('template.renderer');

		// Set the class name for the renderer's service provider
		$class = '\\Joomla\\Service\\' . ucfirst($type) . 'RendererProvider';

		// Sanity check
		if (!class_exists($class))
		{
			throw new \RuntimeException(sprintf('Renderer provider for renderer type %s not found.', ucfirst($type)));
		}

		// Add the provider to the DI container
		$this->getContainer()->registerServiceProvider(new $class);

		return $this->getContainer()->get('renderer');
	}

	/**
	 * Method to initialize the view object
	 *
	 * @return  ViewInterface  View object
	 *
	 * @since   1.0
	 * @throws  \RuntimeException
	 */
	protected function initializeView()
	{
		// Initialize the model object
		$model = $this->initializeModel();

		$view   = ucfirst($this->getInput()->getWord('view', $this->defaultView));

		$class = '\\Joomla\\View\\' . $view . 'HtmlView';

		// Ensure the class exists, fall back to default otherwise
		if (!class_exists($class))
		{
			$class = '\\Joomla\\View\\DefaultHtmlView';

			// If we still have nothing, abort mission
			if (!class_exists($class))
			{
				throw new \RuntimeException(sprintf('A view class was not found for the %s view.', $view));
			}
		}

		// HTML views require a renderer object too, fetch it
		$renderer = $this->initializeRenderer();

		// Instantiate the view now
		/* @type  \Joomla\View\AbstractHtmlView  $object */
		$object = new $class($model, $renderer);

		// We need to set the layout too
		$object->setLayout(strtolower($view) . '.' . strtolower($this->getInput()->getWord('layout', 'index')));

		return $object;
	}
}
```

The view class in this example is extended from this sample class which extends `\Joomla\View\AbstractView`:
```php
namespace Joomla\View;

use Joomla\Renderer\RendererInterface;

use Joomla\Model\ModelInterface;
use Joomla\View\AbstractView;

/**
 * Abstract HTML View class
 *
 * @since  1.0
 */
abstract class AbstractHtmlView extends AbstractView
{
	/**
	 * The data array to pass to the renderer engine
	 *
	 * @var    array
	 * @since  1.0
	 */
	private $data = array();

	/**
	 * The name of the layout to render
	 *
	 * @var    string
	 * @since  1.0
	 */
	private $layout;

	/**
	 * The renderer object
	 *
	 * @var    RendererInterface
	 * @since  1.0
	 */
	private $renderer;

	/**
	 * Class constructor
	 *
	 * @param   ModelInterface     $model     The model object.
	 * @param   RendererInterface  $renderer  The renderer object.
	 *
	 * @since   1.0
	 */
	public function __construct(ModelInterface $model, RendererInterface $renderer)
	{
		parent::__construct($model);

		$this->setRenderer($renderer);
	}

	/**
	 * Retrieves the data array
	 *
	 * @return  array
	 *
	 * @since   1.0
	 */
	public function getData()
	{
		return $this->data;
	}

	/**
	 * Retrieves the layout name
	 *
	 * @return  string
	 *
	 * @since   1.0
	 * @throws  \RuntimeException
	 */
	public function getLayout()
	{
		if (is_null($this->layout))
		{
			throw new \RuntimeException('The layout name is not set.');
		}

		return $this->layout;
	}

	/**
	 * Retrieves the renderer object
	 *
	 * @return  RendererInterface
	 *
	 * @since   1.0
	 */
	public function getRenderer()
	{
		return $this->renderer;
	}

	/**
	 * Method to render the view.
	 *
	 * @return  string  The rendered view.
	 *
	 * @since   1.0
	 * @throws  \RuntimeException
	 */
	public function render()
	{
		return $this->getRenderer()->render($this->getLayout(), $this->getData());
	}

	/**
	 * Sets the data array
	 *
	 * @param   array  $data  The data array.
	 *
	 * @return  $this  Method allows chaining
	 *
	 * @since   1.0
	 */
	public function setData(array $data)
	{
		$this->data = $data;

		return $this;
	}

	/**
	 * Sets the layout name
	 *
	 * @param   string  $layout  The layout name.
	 *
	 * @return  $this  Method allows chaining
	 *
	 * @since   1.0
	 */
	public function setLayout($layout)
	{
		$this->layout = $layout;

		return $this;
	}

	/**
	 * Sets the renderer object
	 *
	 * @param   RendererInterface  $renderer  The renderer object.
	 *
	 * @return  $this  Method allows chaining
	 *
	 * @since   1.0
	 */
	public function setRenderer(RendererInterface $renderer)
	{
		$this->renderer = $renderer;

		return $this;
	}
}
```

The view class for our view as established in the above sample controller:
```php
namespace Joomla\View;

use Joomla\Model\DefaultModel;

/**
 * HTML view class for the Dashboard
 *
 * @since  1.0
 */
class DashboardHtmlView extends AbstractHtmlView
{
	/**
	 * Redeclared model object for proper typehinting
	 *
	 * @var    DefaultModel
	 * @since  1.0
	 */
	protected $model;

	/**
	 * Method to render the view.
	 *
	 * @return  string  The rendered view.
	 *
	 * @since   1.0
	 */
	public function render()
	{
		$this->setData(['data' => $this->model->getData()]);

		return parent::render();
	}
}
```

## Installation via Composer

You can simply run the following from the command line:

```sh
composer require joomla/renderer
```
