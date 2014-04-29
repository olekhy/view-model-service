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
	 * @var Closure
	 */
	protected $callable;

	public function __construct($name, $callable, $classModel, $classMapper = null)
	{
		$this->setName($name);
		$this->setCallable($callable);
		$this->setClassModel($classModel);
		$this->setClassMapper($classMapper);
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
			throw new InvalidArgumentException(sprintf('Invalid argument "%s" expected typ of callable', gettype($callable)));
		}
		$this->callable = $callable;
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
	public function hasMapper()
	{
		return isset($this->classMapper);
	}
	/**
	 * @param ViewModelInterface $model
	 * @param $callable
	 * @return ViewMapperInterface
	 */
	public function getMapper(ViewModelInterface $model, $callable)
	{
		$mapper = $this->getClassMapper();
		return new $mapper($model, $callable);
	}

	/**
	 * @param null|mixed $data
	 * @return ViewModelInterface
	 */
	public function getModel($data = null)
	{
		$model = $this->getClassModel();
		return new $model($data);
	}
}

