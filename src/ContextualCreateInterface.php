<?php
namespace ViewModelService;

use ViewModelService\ViewModel\ViewModelInterface;

/**
 * Interface ContextualCreateInterface
 *
 * @package ViewModelService
 */
interface ContextualCreateInterface
{
	/**
	 * @param CreationRecipe $recipe
	 * @return ViewModelInterface
	 */
	public function createViewModel(CreationRecipe $recipe);

	/**
	 * @param ContextualCreateInterface $context
	 * @return mixed
	 */
	public function appendChainedContext(ContextualCreateInterface $context);

	/**
	 * @return mixed
	 */
	public function setUpContext();

	/**
	 * @return mixed
	 */
	public function closeContext();
}
