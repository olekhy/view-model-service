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
		$classModel = __NAMESPACE__ . '\\TestAsset\ViewModel\SuperViewModel';
		$classMapper = __NAMESPACE__ . '\\TestAsset\ViewMapper\SuperViewMapper';;
		$utt = new CreationRecipe('test', $callable, $classModel, $classMapper);

		$this->assertTrue(is_callable($utt->getCallable()));
		$this->assertInstanceOf('ViewModelService\ViewMapper\ViewMapperInterface', $utt->getMapper(new $classModel, $callable));
		$this->assertInstanceOf('ViewModelService\ViewModel\ViewModelInterface', $utt->getModel());

	}

	/**
	 * @expectedException InvalidArgumentException
	 * @expectedExceptionMessage Invalid argument "string" expected typ of callable
	 */
	public function testThrowExceptionWhenNotACallableProvided()
	{
		$callable = 'string is not a callable';
		$classModel = __NAMESPACE__ . '\\TestAsset\SuperViewModel';
		$classMapper = __NAMESPACE__ . '\\TestAsset\SuperViewMapper';;
		$utt = new CreationRecipe('test', $callable, $classModel, $classMapper);
	}

	/**
	 * @expectedException InvalidArgumentException
	 * @expectedExceptionMessage  Invalid argument "array" expected typ of callable
	 */
	public function testInvalidArgumentCallableException()
	{
		$utt = new CreationRecipe('name_1', array(), 'classname');
	}

	/**
	 * @expectedException InvalidArgumentException
	 * @expectedExceptionMessage  Class "classname" does not exists
	 */
	public function test()
	{
		$utt = new CreationRecipe('name_2', function(){return array();}, 'classname');
	}
}

