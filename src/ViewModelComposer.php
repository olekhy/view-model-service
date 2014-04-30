<?php
namespace ViewModelService;

use LogicException;
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

	/**
	 * @var CreateViewModelBehaviourInterface[]
	 */
	protected $behaviour;

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

		if (isset($options['behaviour_on_create']))
		{
			if (!is_array($options['behaviour_on_create']))
			{
				$options['behaviour_on_create'] = array($options['behaviour_on_create']);
			}
			foreach ($options['behaviour_on_create'] as $behaviour)
			{
				$this->registerBehaviourOnCreateModel($behaviour);
			}
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
		return new CreationRecipe($receptionId, $callable, $classNameViewModel, $classNameViewModelMapper);
	}

	/**
	 * @param CreationRecipe $recipe
	 * @return ViewModelInterface
	 */
	public function composeFromRecipe(CreationRecipe $recipe)
	{
		$this->applyBehaviour();
		$model = $recipe->createViewModel();
		$this->revertBehaviour();
		return $model;
	}



	/**
	 * @param CreateViewModelBehaviourInterface $behavior
	 * @throws LogicException
	 */
	protected function registerBehaviourOnCreateModel(CreateViewModelBehaviourInterface $behavior)
	{
		$hash = spl_object_hash($behavior);
		if(isset($this->behaviour[$hash]))
		{
			throw new LogicException(sprintf('Behaviour "%s" of same typ is already registered. You need remove this one first',
							get_class($behavior))
			);
		}
		$this->behaviour[$hash] = $behavior;
	}

	/**
	 * @param CreateViewModelBehaviourInterface $behavior
	 */
	protected function unRegisterBehaviourOnCreateModel(CreateViewModelBehaviourInterface $behavior)
	{
		unset($this->behaviour[spl_object_hash($behavior)]);
	}

	/**
	 * @return $this
	 */
	protected function applyBehaviour()
	{
		$this->processBehaviour('on');
		return $this;
	}

	/**
	 * @return $this
	 */
	protected function revertBehaviour()
	{
		$this->processBehaviour('off');
		return $this;
	}

	/**
	 * @param string $typ on or off
	 */
	protected function processBehaviour($typ)
	{
		if (!empty($this->behaviour))
		{
			$method = strtolower($typ);
			foreach ($this->behaviour as $behaviour)
			{
				$behaviour->$method();
			}
		}
	}
}
