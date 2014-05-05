<?php
namespace ViewModelServiceTest;

use PHPUnit_Framework_TestCase as TestCase;
use ViewModelService\CreationRecipe;
use ViewModelService\ViewModelComposer;

/**
 * Class ViewModelComposerTest
 *
 * @package ViewModelServiceTest
 */
class ViewModelComposerTest extends TestCase
{

	public function testCanGetRecipeWithNormalCallable()
	{
		$composer = new ViewModelComposer(array('namespace' =>__NAMESPACE__ . '\\TestAsset'));
		$callable = function(){return array(1,2,4);};
		$recipe = $composer->getRecipe('Super', $callable);
		$this->assertSame($callable, $recipe->getCallable());
		$this->assertSame('Super', $recipe->getName());
		$this->assertInstanceOf('ViewModelService\CreationRecipe', $recipe);
		$this->assertInstanceOf('ViewModelServiceTest\TestAsset\ViewMapper\SuperViewMapper', $recipe->getMapper($recipe->getModel(), $recipe->getCallable()));
		$this->assertInstanceOf('ViewModelServiceTest\TestAsset\ViewModel\SuperViewModel', $recipe->getModel());
	}

	public function testCanGetRecipeWithNotCallable()
	{
		$composer = new ViewModelComposer(array('namespace' =>__NAMESPACE__ . '\\TestAsset'));
		$callable = array(1,2,34); // not a callable
		$recipe = $composer->getRecipe('Super', $callable);
		$this->assertTrue(is_callable($recipe->getCallable()));
		$this->assertSame($callable, call_user_func($recipe->getCallable()));
	}

	public function testCanGetRecipeWithEmptyMapper()
	{
		$composer = new ViewModelComposer(array('namespace' =>__NAMESPACE__ . '\\TestAsset'));
		$callable = function() {return 123;}; // not a callable
		$recipe = $composer->getRecipe('Rimma', $callable);
		$this->assertInstanceOf('ViewModelService\CreationRecipe', $recipe);
	}

	public function testCanComposeWithMapper()
	{
		$composer = new ViewModelComposer();

		$data = array(
			'data1' => 'A',
			'data2' => 'B',
			'data3' => 'C',
			'data4' => 'D',
			'data5' => 'E',
		);

		$callable = function() use ($data)
		{
			return $data;
		};
		$recipe = new CreationRecipe(
				'name_1',
				$callable,
				'ViewModelServiceTest\TestAsset\ViewModel\SuperViewModel',
				'ViewModelServiceTest\TestAsset\ViewMapper\SuperViewMapper'
		);


		$model = $composer->composeFromRecipe($recipe);

		$this->assertInstanceOf('ViewModelServiceTest\TestAsset\ViewModel\SuperViewModel', $model);
		$this->assertSame($data, get_object_vars($model));
	}

	public function testCanComposeUsingOnlyModelClass()
	{
		$composer = new ViewModelComposer();

		$data = array(
			'data1' => 'A',
			'data2' => 'B',
			'data3' => 'C',
			'data4' => 'D',
			'data5' => 'E',
		);

		$callable = function() use ($data)
		{
			return $data;
		};
		$recipe = new CreationRecipe(
				'name_2',
				$callable,
				'ViewModelServiceTest\TestAsset\ViewModel\RimmaViewModel'
		);


		$model = $composer->composeFromRecipe($recipe);

		$this->assertInstanceOf('ViewModelServiceTest\TestAsset\ViewModel\RimmaViewModel', $model);

		$expectation = Array (
			'att_1' => 'data1A',
			'att_2' => 'data2B',
			'att_3' => 'data3C'
		);

		$this->assertSame($expectation, get_object_vars($model));
	}

}
