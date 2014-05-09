<?php
namespace ViewModelService;

use BadMethodCallException;
use InvalidArgumentException;
use LogicException;
use UnexpectedValueException;
use ViewModelService\ViewModel\ViewModelInterface;

/**
 * Class ViewModelRepo
 *
 * @package ViewModelService
 *
 * @method getExample($optionalPostfix = null)
 * @method ViewModelRepo addExample($callable, $optionalPostfix = null)
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
	 *
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
	 * @param  string $name
	 * @param  array  $arguments
	 * @return string Specific name
	 * @throws LogicException
	 * @throws UnexpectedValueException
	 */
	protected function attachRecipe($name, array $arguments)
	{
		$specificName = $name;
		$callable = null;
		$numOfArgs = count($arguments);

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
			throw new UnexpectedValueException(
					'Recipe can not be attached because does not known about handling with less than one or more than two arguments');
		}

		if (isset($this->recipes[$specificName]))
		{
			throw new LogicException(sprintf('You try to override already exists recipe "%s"', $specificName));
		}

		$composer = $this->getViewModelComposer();
		$this->recipes[$specificName] = $composer->getRecipe($name, $callable);
		return  isset($optionalName) ? $optionalName : $specificName;
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
				'Please use method %1$s::add<NameOfView>($callable, $optionalPostfix = null) to map data to a view model or %1$s::get<NameOfView>($optionalPostfix = null) to get view model from repo',
				get_class($this)));
	}

	/**
	 * @param $name
	 * @param $arguments
	 * @return string
	 */
	protected function add($name, $arguments)
	{
		return $this->attachRecipe($name, $arguments);
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

		if (null === $list)
		{
			throw new InvalidArgumentException('Empty data not expected');
		}

		foreach ($list as $id => $argument)
		{
			$return[] = $this->doMethodCall($type, $name, array($argument, $id));
		}
		return $return;
	}
}
