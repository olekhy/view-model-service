<?php
namespace ViewModelService;

use ViewModelService\ViewModel\ViewModelInterface;

/**
 * Class ViewModelComposer
 *
 * @package ViewComposer
 */
class ViewModelComposer
{
	public function getRecipe($receptionId, $callable)
	{
		$classNameViewModel = __NAMESPACE__ . '\\ViewModel\\' . $receptionId . '\\ViewModel';
		$classNameViewModelMapper = __NAMESPACE__ . '\\ViewMapper\\' . $receptionId . '\\ViewMapper';
		if (!is_callable($callable))
		{
			$callable = function($callable)
			{
				return $callable;
			};
		}
		return new CreationRecipe($receptionId, $callable, $classNameViewModel, $classNameViewModelMapper);
	}

	/**
	 * @param CreationRecipe $reception
	 * @return ViewModelInterface
	 */
	public function composeFromRecipe(CreationRecipe $reception)
	{
		$model = $reception->getModel();
		$mapper = $reception->getMapper();
		$mapper->setDataAwareCallable($reception->getCallable())->setViewModel($model);
		return $mapper->map();
	}
}
