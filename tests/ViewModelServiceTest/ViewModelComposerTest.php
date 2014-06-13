<?php
namespace ViewModelServiceTest;

use PHPUnit_Framework_TestCase as TestCase;
use ViewModelService\ViewModelComposer;
use ViewModelServiceTest\TestAsset\ContextA;
use ViewModelServiceTest\TestAsset\ContextB;
use ViewModelServiceTest\TestAsset\ContextC;
use ViewModelServiceTest\TestAsset\ContextCollector;

/**
 * Class ViewModelComposerTest
 *
 * @package ViewModelServiceTest
 */
class ViewModelComposerTest extends TestCase
{


	public function testCanGetRecipe()
	{
		$data = array(
				'data1' => 'aaA',
				'data2' => 'bbbB',
				'data3' => 'ccccC',
				'data4' => 'dddddD',
				'data5' => 'eEe',
		);
		$utt = new ViewModelComposer(array('namespace' => __NAMESPACE__ . '\\TestAsset'));
		$recipe = $utt->getRecipe('Super', $data);
		$this->assertInstanceOf('ViewModelService\CreationRecipe', $recipe);
		$this->assertSame('Super', $recipe->getName());
		$this->assertSame($data, call_user_func($recipe->getCallable()));
		$this->assertSame('ViewModelServiceTest\TestAsset\ViewMapper\SuperViewMapper',$recipe->getClassMapper());
		$this->assertSame('ViewModelServiceTest\TestAsset\ViewModel\SuperViewModel',$recipe->getClassModel());

		return $recipe;
	}

	/**
	 * @depends testCanGetRecipe
	 */
	public function test_Can_Get_Recipe_Without_Namespace()
	{
		$utt = new ViewModelComposer(array('namespace' => false));
		$recipe = $utt->getRecipe('MapperForExample', 'string');
		$this->assertSame('MapperForExampleViewMapper',$recipe->getClassMapper());
	}

	/**
	 * @depends testCanGetRecipe
	 */
	public function test_Can_Get_Recipe_With_Specific_Namespace()
	{
		$utt = new ViewModelComposer(array('namespace' => 'NAME_SPACE'));
		$recipe = $utt->getRecipe('MapperForExample', 'string');
		$this->assertSame('NAME_SPACE\ViewMapper\MapperForExampleViewMapper',$recipe->getClassMapper());
	}

	/**
	 * @depends testCanGetRecipe
	 */
	public function test_Can_Get_Recipe_With_Default_Namespace()
	{
		$utt = new ViewModelComposer();
		$recipe = $utt->getRecipe('MapperForExample', 'string');
		$this->assertSame('ViewModelService\ViewMapper\MapperForExampleViewMapper',$recipe->getClassMapper());
	}

	/**
	 * @param $recipe
	 * @depends testCanGetRecipe
	 */
	public function test_Can_Compose_View_Model($recipe)
	{
		$utt = new ViewModelComposer();
		$model = $utt->composeFromRecipe($recipe);
		$expectation = array(
				'data1' => 'aaA',
				'data2' => 'bbbB',
				'data3' => 'ccccC',
				'data4' => 'dddddD',
				'data5' => 'eEe',
		);
		$this->assertSame($expectation, get_object_vars($model));
	}

	/**
	 * @param $recipe
	 * @depends testCanGetRecipe
	 */
	public function test_Can_Compose_View_Model_Within_Context($recipe)
	{
		$collector = new ContextCollector();
		$options = array('context' => array(
			new ContextA($collector),
			new ContextB($collector),
			new ContextC($collector),
		));
		$utt = new ViewModelComposer($options);
		$model = $utt->composeFromRecipe($recipe);

		$expectation = array (
				0 => 'ViewModelServiceTest\\TestAsset\\ContextA::setUpContext',
				1 => 'ViewModelServiceTest\\TestAsset\\ContextB::setUpContext',
				2 => 'ViewModelServiceTest\\TestAsset\\ContextC::setUpContext',
				3 => 'ViewModelServiceTest\\TestAsset\\ContextC::closeContext',
				4 => 'ViewModelServiceTest\\TestAsset\\ContextB::closeContext',
				5 => 'ViewModelServiceTest\\TestAsset\\ContextA::closeContext',
		);
		$this->assertSame($expectation, $collector->getCollection());
	}







}
