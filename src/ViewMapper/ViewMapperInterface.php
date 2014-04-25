<?php
namespace ViewModelService\ViewMapper;

use ViewModelService\ViewModel\ViewModelInterface;

/**
 * Interface ViewMapperInterface
 *
 * @package ViewComposer\ViewMapper
 */
interface ViewMapperInterface
{
	/**
	 * @return ViewModelInterface
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
}
