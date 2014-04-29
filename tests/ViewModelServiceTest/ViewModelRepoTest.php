<?php
namespace ViewModelServiceTest;

use BadMethodCallException;
use InvalidArgumentException;
use LogicException;
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
	 * @expectedExceptionMessage Could not create view model from not exists recipe by name: Super
	 */
	public function testShouldThrowExceptionIfInvalidRecipe()
	{
		$view = $this->repo->getSuper();
	}

	/**
	 * @expectedException LogicException
	 * @expectedExceptionMessage You try to override already exists recipe "Super"
	 */
	public function testShouldThrowExceptionIfRecipeWasAddedTwice()
	{
		$this->repo->addSuper(array(1));
		$this->repo->addSuper(array(2));
	}

	public function testShouldCreateMultipleViewModelsBySameTypAndDifferentContent()
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
				'data4' => 'Lisett',
				'data5' => 'Rimma',
		);

		$this->repo->addSuper($data_1);
		$this->repo->addSuper($data_2, 'special_model');
		$this->repo->addSuper($data_3, 'id_B');


		$view_1 = $this->repo->getSuper();
		$view_2 = $this->repo->getSuper('special_model');
		$view_3 = $this->repo->getSuper('id_B');

		$this->assertSame($data_1, get_object_vars($view_1));
	  $this->assertSame($data_2, get_object_vars($view_2));
	  $this->assertSame($data_3, get_object_vars($view_3));

	}

	/**
	 * @expectedException BadMethodCallException
	 * @expectedExceptionMessage Please use method ViewModelService\ViewModelRepo::add<NameOfView>($callable, $optionalPostfix = null) to map data to a view model
	or ViewModelService\ViewModelRepo::get<NameOfView>($optionalPostfix = null) to get view model from repo
	 */
	public function testShouldThrowExceptionWhenTryingToWorkWithMagicalCallOfAddDataOrGetViewModel()
	{
			$this->repo->createBestEverViewModel();
	}

	public function testHasAlwaysComposer()
	{
		ViewModelRepo::resetRepo();
		$this->assertInstanceOf('ViewModelService\ViewModelComposer', 	ViewModelRepo::getRepo()->getViewModelComposer());
	}

	/**
	 * @expectedException LogicException
	 * @expectedExceptionMessage Singleton is not cloneable
	 */
	public function testShouldTrowExceptionAtCloning()
	{
		$r1 = clone $this->repo;
	}

	/**
	 * @expectedException LogicException
	 * @expectedExceptionMessage Singleton un serializing is invalid approach
	 */
	public function testShouldTrowExceptionAtWakeUp()
	{
		$string = serialize($this->repo);
		unserialize($string);
	}
}
