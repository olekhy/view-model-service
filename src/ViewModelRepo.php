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
 * @method getExample($optionalPostfix = null)
 * @method addExample($callable, $optionalPostfix = null)
 */
class ViewModelRepo
{
	protected static $instance;

	/**
	 * @var CreationRecipe[]
	 */
	protected $receptions;

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

	}

	final public function __wakeup()
	{

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
	public function resetRepo()
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

		if (isset($arguments[1]))
		{
			$name = $this->getExtendedName($name, $arguments[1]);
		}

		if (strcasecmp('add', $type) !== 0)
		{
			$this->attachRecipe($type, $arguments[0]);
		}
		elseif (strcasecmp('get', $type) !== 0)
		{
			return $this->getModel($name);
		}
		else
		{
			throw new BadMethodCallException(sprintf(
					'Please use method %1$s::add<NameOfViewModel>($callable, $optionalPostfix = null) to map data to a view model
					 and %1$s::add|get<NameOfViewModel>($optionalPostfix = null) get view model',
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
		if (null === $this->receptions[$name])
		{
			throw new InvalidArgumentException('Could not create view model from not exists reception by name:' . $name);
		}

		if (null === $this->models[$name])
		{
			$composer = $this->getViewModelComposer();
			$this->models[$name] = $composer->composeFromRecipe($this->receptions[$name]);
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
	 * @param string $type
	 * @param mixed  $argument
	 * @throws LogicException
	 */
	protected function attachRecipe($type, $argument)
	{
		if (null !== $this->receptions[$type])
		{
			throw new LogicException('You try to override already exists reception for creation of the view model');
		}

		$composer = $this->getViewModelComposer();
		$this->receptions[$type] = $composer->getRecipe($type, $argument);
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
