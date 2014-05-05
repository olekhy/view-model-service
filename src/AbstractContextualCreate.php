<?php
/**
 * Created by PhpStorm.
 * User: al
 * Date: 5/2/14
 * Time: 1:00 PM
 */

namespace ViewModelService;


use ViewModelService\ViewModel\ViewModelInterface;

abstract class AbstractContextualCreate implements ContextualCreateInterface
{
	/**
	 * @var ContextualCreateInterface
	 */
	protected $chainedContext;

	/**
	 * @var CreationRecipe
	 */
	protected $recipe;

	/**
	 * @param CreationRecipe $recipe
	 * @return ViewModelInterface
	 */
	public function createViewModel($recipe)
	{
		$this->recipe = $recipe;

		$this->setUpContext();

		if (null !== $this->chainedContext)
		{
			$model = $this->chainedContext->createViewModel($recipe);
			if (null !== $model)
			{
				$this->closeContext();
			}
		}
		else
		{
			$model = $recipe->createViewModel();
			$this->closeContext();
		}

		return $model;
	}

	/**
	 * @param ContextualCreateInterface $context
	 * @return $this
	 */
	public function appendChainedContext(ContextualCreateInterface $context)
	{
		$this->chainedContext = $context;
		return $this;
	}
}
