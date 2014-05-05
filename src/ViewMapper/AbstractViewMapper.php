<?php
namespace ViewModelService\ViewMapper;

use InvalidArgumentException;
use ViewModelService\ViewModel\ViewModelInterface;

/**
 * Class AbstractViewMapper
 *
 * @package ViewmodelService\ViewMapper
 */
abstract class AbstractViewMapper implements ViewMapperInterface
{
	/**
	 * @param ViewModelInterface $model
	 * @param $callable
	 */
	public function __construct(ViewModelInterface $model, $callable)
	{
		$this->model = $model;
		$this->setDataAwareCallable($callable);
	}

	/**
	 * @var ViewModelInterface
	 */
	protected $model;

	/**
	 * @var Callable
	 */
	protected $dataAware;

	/**
	 * @inheritdoc
	 */
	public function setViewModel(ViewModelInterface $model)
	{
		$this->model = $model;
		return $this;
	}

	/**
	 * @inheritdoc
	 */
	public function setDataAwareCallable($callable)
	{
		if (!is_callable($callable))
		{
			throw new InvalidArgumentException('Invalid argument, expected of callable typ');
		}
		$this->dataAware = $callable;
		return $this;
	}

	/**
	 * @inheritdoc
	 */
	public function getDataForMapping()
	{
		$data = $this->dataAware;
		return $data();
	}

	/**
	 * @inheritdoc
	 */
	public function getViewModelComplete()
	{
		return $this->map($this->getDataForMapping());
	}
}
