<?php
namespace ViewModelService;

use BadMethodCallException;
use InvalidArgumentException;
use LogicException;
use ViewModelService\ViewModel\ViewModelInterface;

/**
 * Class ViewModelRepo
 *
 * @package ViewModelService
 *
 * Syntax for class methods that called via magic __call() is: @method [return type] [name]([[type] [parameter]<, ...>]) [<description>]
 */
class ViewModelRepo
{
	protected static $instance;

	/**
	 * @var CreationRecipe[]
	 */
	protected $recipes;

	/**
	 * @var array View models instances
	 */
	protected $models;

	/**
	 * @var ViewModelComposer
	 */
	protected $viewModelComposer;

	/**
	 * @var array of ids of $this->recipes by view model name
	 */
	protected $collectionsIds;

	protected function __construct()
	{
	}

	final public function __clone()
	{
		throw new LogicException('Singleton is not cloneable');
	}

	final public function __wakeup()
	{
		throw new LogicException('Singleton un serializing is an invalid approach');
	}

	/**
	 * @return static
	 */
	public static function getRepo()
	{
		if (null === static::$instance)
		{
			static::$instance = new static;
		}
		return static::$instance;
	}

	/**
	 * The purpose is reset instance for unit testing fo example
	 */
	public static function resetRepo()
	{
		static::$instance = null;
	}

	/**
	 * Method is proxy to the real execution
	 *
	 * [collection]Add|GetViewModelName($arguments[0]...$arguments[1])
	 *
	 * @param string $methodName
	 * @param array $arguments
	 * @throws BadMethodCallException
	 * @return ViewModelInterface|ViewModelInterface[]|string|array  When returned string or array that is for id names of View Models in repo
	 */
	public function __call($methodName, $arguments)
	{
		if (strcasecmp('collection', substr($methodName, 0, 10)) === 0)
		{
			$typeAndNameViewModel = lcfirst(substr($methodName, 10));
			$realMethodName = 'collection';
		}
		else
		{
			$typeAndNameViewModel = $methodName;
			$realMethodName = 'doMethodCall';
		}

		list($type, $name) = sscanf($typeAndNameViewModel, '%3s%s');

		return $this->$realMethodName($type, $name, $arguments);
	}

	/**
	 * @param $name
	 * @return ViewModelInterface
	 * @throws InvalidArgumentException
	 */
	protected function getModel($name)
	{
		if (!isset($this->recipes[$name]))
		{
			throw new InvalidArgumentException(sprintf(
					'Could not create view model from not exists recipe by name: %s, maybe was never added', $name));
		}

		if (!isset($this->models[$name]))
		{
			$composer = $this->getViewModelComposer();
			$this->models[$name] = $composer->composeFromRecipe($this->recipes[$name]);
		}

		return $this->models[$name];
	}

	/**
	 * @param $postfix
	 * @param $name
	 * @return string
	 */
	protected function getExtendedName($name, $postfix)
	{
		$name .= $postfix;
		return $name;
	}

	/**
	 * @param CreationRecipe $recipe
	 * @param string         $specificName
	 * @param string|null    $optionalName
	 * @throws LogicException
	 * @return string Name in repo
	 */
	protected function attachRecipe(CreationRecipe $recipe, $specificName, $optionalName = null)
	{
		if (!isset($this->recipes[$specificName]))
		{
			$this->recipes[$specificName] = $recipe;
			//return  isset($optionalName) ? $optionalName : $specificName;
			return $specificName;
		}

		if (!isset($optionalName))
		{
			$hash = spl_object_hash($recipe);
			$specificName = $specificName . $hash;
			$this->recipes[$specificName] = $recipe;
			return $specificName;
		}

		throw new LogicException(sprintf('Recipe "%s"  with optional name "%s" is already registered', $recipe->getName(), $optionalName));
		}

	/**
	 * @param string $name
	 * @param mixed $callable
	 * @return CreationRecipe
	 */
	protected function getRecipe($name, $callable)
		{
			$composer = $this->getViewModelComposer();
		return $composer->getRecipe($name, $callable);
		}

	/**
	 * @param ViewModelComposer $viewModelComposer
	 * @return $this
	 */
	public function setViewModelComposer(ViewModelComposer $viewModelComposer)
	{
		$this->viewModelComposer = $viewModelComposer;
		return $this;
	}

	/**
	 * @return ViewModelComposer
	 */
	public function getViewModelComposer()
	{
		if (null === $this->viewModelComposer)
		{
			$this->viewModelComposer = new ViewModelComposer();
		}
		return $this->viewModelComposer;
	}

	/**
	 * @param $type
	 * @param $name
	 * @param $arguments
	 * @return $this|ViewModelInterface
	 * @throws BadMethodCallException
	 */
	protected function doMethodCall($type, $name, $arguments)
	{
		if (method_exists($this, $type))
		{
			return $this->$type($name, $arguments);
		}

		throw new BadMethodCallException(sprintf(
				'Invalid method called "%1$s::%2$s%3$s()", for adding or getting ViewModel(s) only allowed "add<ViewModel>()" or "get<ViewModel>()"',
				get_class($this),
				$type,
				$name)
		);
	}

	/**
	 * @param $name
	 * @param $arguments
	 * @throws InvalidArgumentException
	 * @return string
	 */
	protected function add($name, $arguments)
	{
		$specificName = $name;
		$callable = null;
		$numOfArgs = count($arguments);
		$optionalName = null;
		if ($numOfArgs == 2)
		{
			list($callable, $optionalName) = $arguments;
			$specificName = $this->getExtendedName($name, $optionalName);
	    }
		elseif($numOfArgs == 1)
		{
			$callable = array_shift($arguments);
		}
		else
		{
			throw new InvalidArgumentException(
					'Recipe can not be attached because does not known about handling with less than one or more than two arguments');
		}
		$recipe = $this->getRecipe($name, $callable);
		return $this->attachRecipe($recipe, $specificName, $optionalName);
	}

	/**
	 * @param $name
	 * @param $arguments
	 * @return ViewModelInterface
	 */
	protected function get($name, $arguments)
	{
		$name = isset($arguments[0]) ? $this->getExtendedName($name, $arguments[0]) : $name;
		return $this->getModel($name);
	}

	/**
	 * @param $type
	 * @param $name
	 * @param $arguments
	 * @throws InvalidArgumentException
	 * @return array
	 */
	protected function collection($type, $name, $arguments)
	{

		$return = array();
		$list = array_shift($arguments);
		$nameAddOn = array_pop($arguments);
		$backupArgs = func_get_args();
    $originalArgs = $backupArgs[2];
		$isPickUp = null;

		$collectionId = $name . $nameAddOn . 'collection';

		if (!isset($originalArgs[0]) || is_scalar($originalArgs[0]))
		{
			$isPickUp = true;
			$nameAddOn = isset($originalArgs[0]) ? $originalArgs[0] : '';
			$collectionId = $name . $nameAddOn . 'collection';
			$list = (isset($this->collectionsIds[$collectionId]) ? $this->collectionsIds[$collectionId] : array());
		}

		if (!is_array($list))
		{
			throw new InvalidArgumentException(sprintf(
							'Sorry unexpected argument given "%s", expected an array where each element will be used for building a ViewModel named "%s" in the collection',
							var_export($list, true),
							$name
					)
			);
		}
		if (isset($this->collectionsIds[$collectionId]))
		{
			if (isset($nameAddOn) && !$isPickUp)
			{
				throw new LogicException(sprintf('Collection "%s" with optional name "%s" already registered', $name, $nameAddOn));
			}
			$collectionId .= count($this->collectionsIds);
		}

		foreach ($list as $argument)
		{

			if ($isPickUp)
			{

				$argument = substr($argument, strlen($name));
			}
			$repoIdOrModel = $this->doMethodCall($type, $name, array($argument));

			if (null === $isPickUp && !$repoIdOrModel instanceof ViewModelInterface)
			{
				$this->collectionsIds[$collectionId][] = $repoIdOrModel;
			}

				$return[] = $repoIdOrModel;
			}
		if (null === $isPickUp)
		{
			$return = $collectionId;
		}
		return $return;
	}

	/**
	 * Register view model and corresponding data in repository
	 *
	 * Usage example:
	 * <code>
	 *
	 *     $data = ['hello', 'world']
	 *
	 *     $repo->registerModel('Breadcrumb', $data)
	 *
	 *     $model = $repo->getBreadcrumb() // this is a magically method call used __call('get', ['Breadcrumb', ['hello', 'world']])
	 *
	 *     // $model is an instance of BreadcrumbViewModel
	 *
	 *     // with specific id key
	 *
	 *     $repo->registerModel('Breadcrumb', $data, 'mySpecific')
	 *
	 *     $model = $repo->getBreadcrumb('mySpecific')
	 *
	 *     // $model is an instance of BreadcrumbViewModel
	 *
	 *
	 * </code>
	 *
	 * @param string         $name                Name of ViewModel to be register
	 * @param mixed|callable $mixedData           Data that fill the ViewModel
	 * @param null|string    $optionalNamePostfix Postfix string to indicate the specific ViewModel in the repo
	 * @return string                             Returns string as key name of ViewModel in the repository container
	 */
	public function registerModel($name, $mixedData, $optionalNamePostfix = null)
	{
		return $this->add($name, array($mixedData, $optionalNamePostfix));
	}

	/**
	 * Register collection of View Models and corresponding data in repository
	 *
	 * Create an collection of view models for each $data array element
	 * and store array of view model names (ids) in property with $name as the key.
	 *
	 * Example usage:
	 * <code>
	 *
	 *     $breadcrumbData = [ [ 'link' => '/', 'name' => 'Homepage' ], [ 'link' => '/page/post', 'name' => 'post' ] ]
	 *
	 *     $repo->registerModelsCollection('Breadcrumb', $breadcrumbData);  // here we register two BreadcrumbViewModel in repository
	 *
	 *     $breadcrumbViewModels = $repo->collectionGetBreadcrumb(); // here is the magically method calling that uses __call()
	 *
	 *     // or with a specific name when this one was opted at collection register
	 *
	 *     $repo->registerModelsCollection('Breadcrumb', $breadcrumbData, 'mySpecificName');  // here we register two BreadcrumbViewModel in repository
	 *
	 *
	 *     $breadcrumbViewModels = $repo->collectionGetBreadcrumb('mySpecificName');
	 *
	 *     //in both cases $breadcrumbViewModels contains an array BreadcrumbViewModel[]
	 *
	 * </code>
	 *
	 *
	 * @param string      $name                   Name of ViewModel class which is used in collection
	 * @param array       $data                   Data array where each element will applied to corresponding View Model instance in collection
	 * @param null|string $optionalCollectionName Name that used to override default name ($name) of the array contains view model ids
	 * @return array
	 */
	public function registerModelsCollection($name, array $data, $optionalCollectionName = null)
	{
		return $this->collection('add', $name, array($data, $optionalCollectionName));
	}

}

