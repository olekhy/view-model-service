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
		$recipe = $composer->getRecipe('TestAsset', $callable);
		$this->assertSame($callable, $recipe->getCallable());
		$this->assertSame('TestAsset', $recipe->getName());
		$this->assertInstanceOf('ViewModelService\CreationRecipe', $recipe);
		$this->assertInstanceOf('ViewModelServiceTest\TestAsset\ViewMapper\TestAssetViewMapper', $recipe->getMapper());
		$this->assertInstanceOf('ViewModelServiceTest\TestAsset\ViewModel\TestAssetViewModel', $recipe->getModel());

	}

	public function testCanGetRecipeWithNotCallable()
	{
		$composer = new ViewModelComposer();
		$callable = array(1,2,34); // not a callable
		$recipe = $composer->getRecipe('TestAsset', $callable);
		$this->assertTrue(is_callable($recipe->getCallable()));
		$this->assertSame($callable, call_user_func($recipe->getCallable()));
	}

	public function testCanCompose()
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
				'TestAsset',
				$callable,
				'ViewModelServiceTest\TestAsset\ViewModel\TestAssetViewModel',
				'ViewModelServiceTest\TestAsset\ViewMapper\TestAssetViewMapper'
		);


		$model = $composer->composeFromRecipe($recipe);

		$this->assertInstanceOf('ViewModelServiceTest\TestAsset\ViewModel\TestAssetViewModel', $model);
		$this->assertSame($data, get_object_vars($model));
	}
}
