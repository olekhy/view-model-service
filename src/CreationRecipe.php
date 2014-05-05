<?php
namespace ViewModelService;

use Closure;
use InvalidArgumentException;
use ViewModelService\ViewMapper\ViewMapperInterface;
use ViewModelService\ViewModel\ViewModelInterface;

/**
 * Class CreationRecipe
 *
 * @package ViewModelService
 */
class CreationRecipe
{
	/**
	 * @var string
	 */
	protected $classMapper;

	/**
	 * @var string
	 */
	protected $classModel;

	/**
	 * @var string
	 */
	protected $name;

	/**
	 * @var string | bool  when boolean false than no namespace wil be used for look up needed classes
	 */
	protected $namespace;
	/**
	 * @var Closure
	 */
	protected $callable;

	public function __construct($name, $callable, $namespace = false)
	{
		$this->name = $name;
		$this->setCallable($callable);
		$this->namespace = $namespace;
	}

	/**
	 * @param Closure $callable
	 * @throws InvalidArgumentException
	 * @return $this
	 */
	public function setCallable($callable)
	{
		if (!is_callable($callable))
		{
			$this->callable = function() use ($callable)
			{
				return $callable;
			};
		}
		else
		{
			$this->callable = $callable;
		}

		return $this;
	}

	/**
	 * @return Closure
	 */
	public function getCallable()
	{
		return $this->callable;
	}

	/**
	 * @param string $classMapper
	 * @return $this
	 */
	public function setClassMapper($classMapper)
	{
		$this->classMapper = $classMapper;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getClassMapper()
	{
		if (null !== $this->classMapper)
		{
			return $this->classMapper;
		}

		if (false != $this->namespace)
		{
			$this->classMapper = $this->namespace . '\\ViewMapper\\';
		}

		$this->classMapper .= $this->name . 'ViewMapper';

		return $this->classMapper;
	}

	/**
	 * @param string $classModel
	 * @throws InvalidArgumentException
	 * @return $this
	 */
	public function setClassModel($classModel)
	{
		if (!class_exists($classModel))
		{
			throw new InvalidArgumentException(sprintf('Class "%s" does not exists', $classModel));
		}
		$this->classModel = $classModel;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getClassModel()
	{
		if (null !== $this->classModel)
		{
			return $this->classModel;
		}

		if (false != $this->namespace)
		{
			$classModel = $this->namespace . '\\ViewModel\\' . $this->name . 'ViewModel';
		} else
		{
			$classModel = $this->name . 'ViewModel';
		}

		$this->setClassModel($classModel);

		return $this->classModel;
	}

	/**
	 * @param string $name
	 * @return $this
	 */
	public function setName($name)
	{
		$this->name = $name;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * @return bool
	 */
	protected function hasMapper()
	{
		if (class_exists($this->getClassMapper()))
		{
			return true;
		}
		return false;
	}
	/**
	 * @return ViewMapperInterface|bool Return false when mapper can not be constructed
	 */
	protected function getMapper()
	{
		if ($this->hasMapper())
		{
			$mapper = $this->getClassMapper();
			return new $mapper($this->getModel(), $this->callable);
		}
		return false;
	}

	/**
	 * @param null|mixed $data
	 * @return ViewModelInterface
	 */
	protected function getModel($data = null)
	{
		$model = $this->getClassModel();
		return new $model($data);
	}

	/**
	 * @return ViewModelInterface
	 */
	public function createViewModel()
	{
		$mapper = $this->getMapper();
		if (false === $mapper)
		{
			$data = $this->callable;
			return $this->getModel($data());
		}
		else
		{
			return $mapper->getViewModelComplete();
		}
	}
}

