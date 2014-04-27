<?php
namespace ViewModelServiceTest;

use InvalidArgumentException;
use PHPUnit_Framework_TestCase as TestCase;
use ViewModelService\ViewModelComposer;
use ViewModelService\ViewModelRepo;

class ViewModelRepoTest extends TestCase
{
	/**
	 * @var ViewModelRepo
	 */
	protected $repo;

	protected function setUp()
	{
		parent::setUp();
		ViewModelRepo::resetRepo();
		$composer = new ViewModelComposer(array('namespace' =>__NAMESPACE__ . '\\TestAsset'));
		$this->repo = ViewModelRepo::getRepo();
		$this->repo->setViewModelComposer($composer);
	}

	/**
	 * @expectedException InvalidArgumentException
	 * @expectedExceptionMessage Could not create view model from not exists recipe by name: TestAsset
	 */
	public function testShouldThrowExceptionIfInvalidRecipe()
	{
		$view = $this->repo->getTestAsset();
	}

	/**
	 * @expectedException LogicException
	 * @expectedExceptionMessage You try to override already exists recipe "TestAsset"
	 */
	public function testShouldThrowExceptionIfRecipeWasAddedTwice()
	{
		$this->repo->addTestAsset(array(1));
		$this->repo->addTestAsset(array(2));
	}

	public function testShould()
	{
		$data_1 = array(
				'data1' => 'ABC',
				'data2' => 'BCD',
				'data3' => 'CDE',
				'data4' => 'DEF',
				'data5' => 'EFG',
		);

		$data_2 = array(
				'data1' => 'aA',
				'data2' => 'Bb',
				'data3' => 'cC',
				'data4' => 'Dd',
				'data5' => 'eE',
		);

		$data_3 = array(
				'data1' => 'Maria',
				'data2' => 'Petra',
				'data3' => 'Conny',
				'data4' => 'Lisset',
				'data5' => 'P',
		);

		$this->repo->addTestAsset($data_1);
		$this->repo->addTestAsset($data_2, 'special_model');
		$this->repo->addTestAsset($data_3, 'id_B');


		$view_1 = $this->repo->getTestAsset();
		$view_2 = $this->repo->getTestAsset('special_model');
		$view_3 = $this->repo->getTestAsset('id_B');

		$this->assertSame($data_1, get_object_vars($view_1));
	  $this->assertSame($data_2, get_object_vars($view_2));
	  $this->assertSame($data_3, get_object_vars($view_3));

	}
}
