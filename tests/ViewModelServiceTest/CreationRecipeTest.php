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
	public function testThatNotCallableWillBeCallable()
	{
		$utt = new CreationRecipe('name_1', array(), __NAMESPACE__ . '\\TestAsset\ViewMapper\SuperViewMapper');
		$this->assertTrue(is_callable($utt->getCallable()));
	}

	/**
	 * @expectedException InvalidArgumentException
	 * @expectedExceptionMessage  Class "name_2ViewModel" does not exists
	 */
	public function testShouldThrowInvalidArgumentForNotExistsViewModelClassWithoutNamespace()
	{
		$recipe = new CreationRecipe('name_2', function(){return array();});
		$recipe->createViewModel();
	}

	/**
	 * @expectedException InvalidArgumentException
	 * @expectedExceptionMessage  Class "ViewModelServiceTest\ViewModel\name_2ViewModel" does not exists
	 */
	public function testShouldThrowInvalidArgumentForNotExistsViewModelClassLockUpClassInGivenNamespace()
	{
		$recipe = new CreationRecipe('name_2', '', __NAMESPACE__);
		$recipe->createViewModel();
	}

	public function testCanCreateModelUsingOnlyModelClass()
	{
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
				'Rimma',
				$callable,
				'ViewModelServiceTest\TestAsset'
		);

		$this->assertInstanceOf('ViewModelServiceTest\TestAsset\ViewModel\RimmaViewModel', $recipe->createViewModel());

		$expectation = Array (
				'att_1' => 'data1A',
				'att_2' => 'data2B',
				'att_3' => 'data3C'
		);

		$this->assertSame($expectation, get_object_vars($recipe->createViewModel()));
	}

	public function testCanCreateModelWithMapper()
	{
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
				'Super',
				$callable,
				'ViewModelServiceTest\TestAsset'
		);

		$model = $recipe->createViewModel();
		$this->assertInstanceOf('ViewModelServiceTest\TestAsset\ViewModel\SuperViewModel', $model);
		$this->assertSame($data, get_object_vars($model));
	}
}

