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
		throw new LogicException('Singleton un serializing is invalid approach');
	}

	/**
	 * @return self
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
	 * @param string $methodName
	 * @param array  $arguments
	 * @return ViewModelInterface|$this
	 * @throws LogicException
	 * @throws BadMethodCallException
	 */
	public function __call($methodName, $arguments)
	{
		list($type, $name) = sscanf($methodName, '%3s%s');

		if (strcasecmp('add', $type) === 0)
		{
			$this->attachRecipe($name, $arguments);
		}
		elseif (strcasecmp('get', $type) === 0)
		{
			$name = isset($arguments[0]) ? $this->getExtendedName($name, $arguments[0]) : $name;
			return $this->getModel($name);
		}
		else
		{
			throw new BadMethodCallException(sprintf(
					'Please use method %1$s::add<NameOfView>($callable, $optionalPostfix = null) to map data to a view model'
					. PHP_EOL . 'or %1$s::get<NameOfView>($optionalPostfix = null) to get view model from repo',
					get_class($this)
				)
			);
		}
		return $this;
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
			throw new InvalidArgumentException('Could not create view model from not exists recipe by name: ' . $name);
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
	 * @param string $name
	 * @param array $arguments
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
			list($callable, $specificName) = $arguments;
			$specificName = $this->getExtendedName($name, $specificName);
		}
		elseif($numOfArgs == 1)
		{
			$callable = array_shift($arguments);
		}
		else
		{
			throw new UnexpectedValueException('Recipe can not be attached because does not known about handling with more than two arguments');
		}

		if (isset($this->recipes[$specificName]))
		{
			throw new LogicException(sprintf('You try to override already exists recipe "%s"', $specificName));
		}

		$composer = $this->getViewModelComposer();
		$this->recipes[$specificName] = $composer->getRecipe($name, $callable);
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
}
