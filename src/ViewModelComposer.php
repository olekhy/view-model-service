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
			$this->setNs($options['namespace']);
		}
		elseif(false === $options['namespace'])
		{
			$this->ns = false;
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
		$classNameViewModel = (false !== $this->ns ? $this->ns . '\\ViewModel\\' : '') . $receptionId . 'ViewModel';
		$classNameViewModelMapper = (false !== $this->ns ? $this->ns . '\\ViewMapper\\' : '') . $receptionId . 'ViewMapper';
		if (!is_callable($callable))
		{
			$callable = function() use ($callable)
			{
				return $callable;
			};
		}
		return new CreationRecipe($receptionId, $callable, $classNameViewModel, $classNameViewModelMapper);
	}

	/**
	 * @param CreationRecipe $recipe
	 * @return ViewModelInterface
	 */
	public function composeFromRecipe(CreationRecipe $recipe)
	{
		$model = $recipe->getModel();
		$mapper = $recipe->getMapper();
		$mapper->setDataAwareCallable($recipe->getCallable())->setViewModel($model);
		return $mapper->map();
	}

	/**
	 * @return string
	 */
	protected function getNs()
	{
		return $this->ns;
	}

	/**
	 * @param $namespace
	 * @return $this
	 */
	private function setNs($namespace)
	{
		$this->ns = $namespace;
		return $this;
	}
}
