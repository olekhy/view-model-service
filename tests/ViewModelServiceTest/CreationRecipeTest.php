<?php
namespace ViewModelServiceTest;

use InvalidArgumentException;
use PHPUnit_Framework_TestCase as TestCase;
use ViewModelService\CreationRecipe;

/**
 * Class CreationRecipe
 *
 * @package ViewModelServiceTest
 */
class CreationRecipeTest extends TestCase
{

	public function testInstanceIsOk()
	{
		$callable = function () {};
		$classModel = __NAMESPACE__ . '\\TestAsset\ViewModel\TestAssetViewModel';
		$classMapper = __NAMESPACE__ . '\\TestAsset\ViewMapper\TestAssetViewMapper';;
		$utt = new CreationRecipe('test', $callable, $classModel, $classMapper);

		$this->assertTrue(is_callable($utt->getCallable()));
		$this->assertInstanceOf('ViewModelService\ViewMapper\ViewMapperInterface', $utt->getMapper());
		$this->assertInstanceOf('ViewModelService\ViewModel\ViewModelInterface', $utt->getModel());

	}

	/**
	 * @expectedException InvalidArgumentException
	 * @expectedExceptionMessage Invalid argument "string" expected typ of callable
	 */
	public function testThrowExceptionWhenNotACallableProvided()
	{
		$callable = 'string is not a callable';
		$classModel = __NAMESPACE__ . '\\TestAsset\TestAssetViewModel';
		$classMapper = __NAMESPACE__ . '\\TestAsset\TestAssetViewMapper';;
		$utt = new CreationRecipe('test', $callable, $classModel, $classMapper);
	}
}

