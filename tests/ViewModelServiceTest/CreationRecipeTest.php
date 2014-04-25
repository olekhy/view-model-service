<?php
namespace ViewModelServiceTest;

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
		$classModel = __NAMESPACE__ . '\\TestAsset\TestAsset\TestAssetViewModel';
		$classMapper = __NAMESPACE__ . '\\TestAsset\TestAsset\TestAssetViewMapper';;
		$utt = new CreationRecipe('test', $callable, $classModel, $classMapper);

		$this->assertTrue(is_callable($utt->getCallable()));
		$this->assertInstanceOf('ViewMapperInterface', $utt->getMapper());
		$this->assertInstanceOf('ViewModelInterface', $utt->getModel());

	}

	public function testThrowExceptionWhenNotACallableProvided()
	{
		$callable = 'string is not a callable';
		$classModel = __NAMESPACE__ . '\\TestAsset\TestAsset\TestAssetViewModel';
		$classMapper = __NAMESPACE__ . '\\TestAsset\TestAsset\TestAssetViewMapper';;
		$utt = new CreationRecipe('test', $callable, $classModel, $classMapper);
	}
}

