<?php
namespace ViewModelService\ViewMapper;

use ViewModelService\ViewModel\ViewModelInterface;

/**
 * Interface ViewMapperInterface
 *
 * @package ViewModelService\ViewMapper
 */
interface ViewMapperInterface
{
	/**
	 *
	 * @return void
	 */
	public function map();

	/**
	 * @param ViewModelInterface $model
	 * @return $this
	 */
	public function setViewModel(ViewModelInterface $model);

	/**
	 * @param $callable
	 * @return $this
	 */
	public function setDataAwareCallable($callable);

	/**
	 * @return ViewModelInterface
	 */
	public function getViewModelComplete();

	/**
	 * @return mixed
	 */
	public function getDataForMapping();
}
