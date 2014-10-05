<?php
namespace ViewModelService;

use BadMethodCallException;
use InvalidArgumentException;
use LogicException;
use ViewModelService\Exception\CallUndefCollectionException;
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
	/**
	 * @var bool status when add/get a collection
	 */
	private $inCollection;

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
					'Could not create view model from not exists recipe by name: %s, maybe was never added', $name)
			);
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

		if (true !== $this->inCollection &&  !isset($this->recipes[$specificName]))
		{
			$this->recipes[$specificName] = $recipe;
			return $specificName;
			//return  isset($optionalName) ? $optionalName : $specificName;
		}

		if (!isset($optionalName) || true === $this->inCollection)
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
	 * @param string $name
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
		if (isset($arguments[0]))
		{
			if (0 === strpos($arguments[0], $name))
			{
				$name = $arguments[0];
			}
			else
			{
				$name = $this->getExtendedName($name, $arguments[0]);
			}
		}
		return $this->getModel($name);
	}

	/**
	 * @param string $type
	 * @param string $name
	 * @param $arguments
	 * @throws CallUndefCollectionException
	 * @return array
	 */
	protected function collection($type, $name, $arguments)
	{

		$backupArgs = $arguments;
		$list = array_shift($arguments);
		$nameAddOn = array_pop($arguments);
		$isPickUp = false;

		$collectionId = $this->makeCollectionId($name, $nameAddOn);

		if (!isset($backupArgs[0]) || is_scalar($backupArgs[0]))
		{
			$isPickUp = true;
			$nameAddOn = isset($backupArgs[0]) ? $backupArgs[0] : '';

			if (isset($this->collectionsIds[$nameAddOn]))
			{
				$collectionId = $nameAddOn;
			}
			else
			{
				$collectionId = $this->makeCollectionId($name, $nameAddOn);
			}

			if (!isset($this->collectionsIds[$collectionId]))
			{
				throw new CallUndefCollectionException(sprintf('Can not "%s" an undefined collection "%s"', $type, $collectionId));
			}
			$list = (isset($this->collectionsIds[$collectionId]) ? $this->collectionsIds[$collectionId] : array());
		}


		if (isset($this->collectionsIds[$collectionId]))
		{
			$collectionId = $this->makeNewCollectionId($name, $collectionId, $isPickUp, $nameAddOn);
		}

		return $this->handleCollectionElementsByType($list, $type, $name, $collectionId, $isPickUp);
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
	 * @param $collection
	 * @param $type
	 * @param $name
	 * @param $collectionId
	 * @param boolean $isPickUp
	 * @throws InvalidArgumentException
	 * @return array
	 */
	protected function handleCollectionElementsByType($collection, $type, $name, $collectionId, $isPickUp)
	{
		if (!is_array($collection))
		{
			throw new InvalidArgumentException(sprintf(
							'Unexpected argument given "%s", expected an array where each element used for building a ViewModel named "%s" for the collection',
							var_export($collection, true),
							$name
					)
			);
		}

		$return = array();
		$this->inCollection = true;
		foreach ($collection as $argument)
		{

			if (true === $isPickUp)
			{
				$argument = substr($argument, strlen($name));
			}

			$repoIdOrModel = $this->doMethodCall($type, $name, array($argument));

			if (!$isPickUp && !$repoIdOrModel instanceof ViewModelInterface)
			{
				$this->collectionsIds[$collectionId][] = $repoIdOrModel;
			}

			$return[] = $repoIdOrModel;
		}
		$this->inCollection = false;
		if (!$isPickUp)
		{
			$return = $collectionId;
		}

		return $return;
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

	/**
	 * @param $name
	 * @param $nameAddOn
	 * @return string
	 */
	protected function makeCollectionId($name, $nameAddOn)
	{
		$collectionId = $name . $nameAddOn . 'collection';

		return $collectionId;
	}

	/**
	 * @param $name
	 * @param $collectionId
	 * @param boolean $isPickUp
	 * @param $nameAddOn
	 * @return string
	 * @throws \LogicException
	 */
	protected function makeNewCollectionId($name, $collectionId, $isPickUp, $nameAddOn)
	{
		if (isset($nameAddOn) && !$isPickUp)
		{
			throw new LogicException(sprintf('Collection "%s" with optional name "%s" already registered', $name, $nameAddOn));
		}
		$collectionId .= count($this->collectionsIds);

		return $collectionId;
	}

}

