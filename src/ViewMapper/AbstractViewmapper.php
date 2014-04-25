<?php
namespace ViewModelService\ViewMapper;

use ViewModelService\ViewModel\ViewModelInterface;

/**
 * Class AbstractViewMapper
 *
 * @package ViewmodelService\ViewMapper
 */
abstract class AbstractViewMapper implements ViewMapperInterface
{
	/**
	 * @var
	 */
	protected $model;
	/**
	 * @var
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
		$this->dataAware = $callable;
		return $this;
	}
}
