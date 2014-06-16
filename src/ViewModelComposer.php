<?php
namespace ViewModelService;

use InvalidArgumentException;
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
	 * @var string|bool namespace where need look up for classes or FALSE for off the namespace using
	 */
	protected $namespace;

	/**
	 * @var ContextualCreateInterface[]
	 */
	protected $context;

	/**
	 * @var bool
	 */
	protected $hasContext = false;

	public function __construct(array $options = null)
	{
		if (isset($options['namespace']))
		{
			$this->setNamespace($options['namespace']);
		}
		else
		{
			$this->namespace = __NAMESPACE__;
		}

		if (isset($options['context']))
		{
			$this->setContext($options['context']);
		}
	}

	/**
	 * @param string $recipeId
	 * @param $callable
	 * @return CreationRecipe
	 */
	public function getRecipe($recipeId, $callable)
	{
		return new CreationRecipe($recipeId, $callable, $this->namespace);
	}

	/**
	 * @param CreationRecipe $recipe
	 * @return ViewModelInterface
	 */
	public function composeFromRecipe(CreationRecipe $recipe)
	{
		if ($this->hasContext())
		{
			$context = $this->contextChaining($this->context);
			return $context->createViewModel($recipe);
		}
		else
		{
			return $recipe->createViewModel();
		}
	}

	/**
	 * @param string $namespace
	 * @return $this
	 */
	protected function setNamespace($namespace)
	{
		if (null !== $namespace)
		{
			$this->namespace = $namespace;
		}
		return $this;
	}

	/**
	 * @param ContextualCreateInterface[] $context
	 * @return $this
	 * @throws LogicException
	 */
	protected function setContext($context)
	{
		if (!is_array($context))
		{
			$context = array($context);
		}
		$this->context = $context;

		return $this;
	}

	/**
	 * @param ContextualCreateInterface[] $context
	 * @return ContextualCreateInterface
	 * @throws LogicException
	 * @throws InvalidArgumentException
	 */
	protected function contextChaining(array $context)
	{
		foreach ($context as $current)
		{
			if (false !== next($context))
			{
				$current->appendChainedContext(current($context));
			}
		}

		return array_shift($context);
	}

	/**
	 * @return bool
	 */
	protected function hasContext()
	{
		return !empty($this->context);
	}

}
