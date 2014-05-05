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
	/**
	 * @var string  namespace where need look up for classes
	 */
	protected $ns;

	public function __construct($options = null)
	{
		if (isset($options['namespace']))
		{
			$this->ns = $options['namespace'];
		}
		else
		{
			$this->ns = __NAMESPACE__;
		}
	}

	/**
	 * @param $receptionId
	 * @param $callable
	 * @return CreationRecipe
	 */
	public function getRecipe($receptionId, $callable)
	{
		if (!is_callable($callable))
		{
			$callable = function() use ($callable)
			{
				return $callable;
			};
		}

		$classNameViewModel = (false !== $this->ns ? $this->ns . '\\ViewModel\\' : '') . $receptionId . 'ViewModel';
		$classNameViewModelMapper = (false !== $this->ns ? $this->ns . '\\ViewMapper\\' : '') . $receptionId . 'ViewMapper';

		if (!class_exists($classNameViewModelMapper))
		{
			$classNameViewModelMapper = null;
		}
		return new CreationRecipe($receptionId, $callable, $classNameViewModel, $classNameViewModelMapper);
	}

	/**
	 * @param CreationRecipe $recipe
	 * @return ViewModelInterface
	 */
	public function composeFromRecipe(CreationRecipe $recipe)
	{
		if ($recipe->hasMapper())
		{
			$mapper = $recipe->getMapper($recipe->getModel(), $recipe->getCallable());
			return $mapper->getViewModelComplete();
		}
		else
		{
			$data = $recipe->getCallable();
			return $recipe->getModel($data());
		}
	}
}
