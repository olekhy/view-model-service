<?php
namespace ViewModelServiceTest;

use BadMethodCallException;
use InvalidArgumentException;
use LogicException;
use PHPUnit_Framework_Error;
use PHPUnit_Framework_TestCase as TestCase;
use ViewModelService\ViewModelComposer;
use ViewModelServiceTest\TestAsset\ViewModel\FooViewModel;
use ViewModelServiceTest\TestAsset\ViewModelRepo;
use ViewModelServiceTest\TestAsset\ViewModel\RimmaViewModel;

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

	public function testRegisterViewModelByNameAndGetSameViewModelAsGivenOnRegister()
	{
		$this->repo->registerModel('Foo', 1);
		$this->repo->registerModel('Foo', 2, 'test');

		/** @var $fact1 \ViewModelServiceTest\TestAsset\ViewModel\FooViewModel */
		$fact1 = $this->repo->getFoo();
		/** @var $fact2 \ViewModelServiceTest\TestAsset\ViewModel\FooViewModel */
		$fact2 = $this->repo->getFoo('test');

		$this->assertInstanceOf('ViewModelServiceTest\TestAsset\ViewModel\FooViewModel', $fact1);
		$this->assertInstanceOf(get_class($fact1), $fact2);

		$this->assertSame(array(1), $fact1->getBuildArgs());
		$this->assertSame(array(2), $fact2->getBuildArgs());

		$this->assertFalse($fact1 === $fact2);
	}

	public function testShouldRegisterCollectionOfViewModelsAndGettingOfTheSameCollection()
	{
		$data = array(array(1), array(2), array(3));
		$this->repo->registerModelsCollection('Rimma', $data);
		/** @var $fact RimmaViewModel[] */
		$fact = $this->repo->collectionGetRimma();

		$this->assertTrue(3 === count($fact));
		foreach ($fact as $key => $item)
		{
			$this->assertInstanceOf('ViewModelServiceTest\TestAsset\ViewModel\RimmaViewModel', $item);
			$expectation = '0' . ++$key;
			$this->assertSame($expectation, $item->att_1);
		}
	}

	public function testShouldGetTheSameCollectionByUsingOfOptionalCollectionNameAddOn()
	{
		$data = array(
				array('a'),
				array('z'),
				array('hm')
		);

		$this->repo->registerModelsCollection('Foo', $data, 'MySuperCollectionOfSuperViewModels');
		/** @var $fact FooViewModel[] */
		$fact = $this->repo->collectionGetFoo('MySuperCollectionOfSuperViewModels');

		$this->assertTrue(3 === count($fact));
		foreach ($fact as $key => $item)
		{
			$this->assertInstanceOf('ViewModelServiceTest\TestAsset\ViewModel\FooViewModel', $item);
			$expectation = array($data[$key]);
			$this->assertSame($expectation, $item->getBuildArgs());
		}
	}

	/**
	 * @expectedException InvalidArgumentException
	 * @expectedExceptionMessage Recipe can not be attached because does not known about handling with less than one or more than two arguments
	 */
	public function testAddViewModelShouldThrownExceptionByInvalidArgumentsCountLessThanOne()
	{
			$this->repo->addFoo();
	}

	/**
	 * @expectedException InvalidArgumentException
	 * @expectedExceptionMessage Recipe can not be attached because does not known about handling with less than one or more than two arguments
	 */
	public function testAddViewModelShouldThrownExceptionByInvalidArgumentsCountMoreThanTwo()
	{
			$this->repo->addFoo(array(1,2,3), 'name-add-on', 'unexpected argument');
	}

	/**
	 * @expectedException PHPUnit_Framework_Error
	 * @expectedExceptionMessage Undefined index: Foo
	 */
	public function testCanNorGetTheCollectionByUsingOfBadOptionalCollectionNameAddOn()
	{

		$data = array(
				array('lipsum')
		);

		$this->repo->registerModelsCollection('Foo', $data, 'MySuperCollectionOfSuperViewModels');
		/** @var $fact FooViewModel[] */
		$fact = $this->repo->collectionGetFoo();
	}

	/**
	 * @expectedException InvalidArgumentException
	 * @expectedExceptionMessage Sorry unexpected argument given "NULL", expected an array that each element will be used for building a ViewModel named "Rimma" in the collection
	 */
	public function testExceptionShouldBeThrownAtRegisterOfCollectionOfViewModelsByInvalidFittingDataNotArray_Empty()
	{
		$this->repo->collectionAddRimma();
	}

	/**
	 * @expectedException InvalidArgumentException
	 * @expectedExceptionMessage Sorry unexpected argument given "stdClass::__set_state(array(
	))", expected an array that each element will be used for building a ViewModel named "Rimma" in the collection
	 */
	public function testExceptionShouldBeThrownAtRegisterOfCollectionOfViewModelsByInvalidFittingDataNotArray_InTraversableInstance()
	{
		$this->repo->collectionAddRimma(new \stdClass);
	}

	/**
	 * @expectedException InvalidArgumentException
	 * @expectedExceptionMessage Sorry unexpected argument given "NULL", expected an array that each element will be used for building a ViewModel named "Rimma" in the collection
	 */
	public function testExceptionShouldBeThrownAtRegisterOfCollectionOfViewModelsByInvalidFittingDataNotArray_Null()
	{
		$this->repo->collectionAddRimma(null);
	}

	/**
	 * @expectedException InvalidArgumentException
	 * @expectedExceptionMessage Sorry unexpected argument given "NULL", expected an array that each element will be used for building a ViewModel named "Rimma" in the collection
	 */
	public function testExceptionShouldBeThrownAtRegisterOfCollectionOfViewModelsByInvalidFittingDataNotArray_String()
	{
		$this->repo->collectionAddRimma('');
	}

	/**
	 * @expectedException InvalidArgumentException
	 * @expectedExceptionMessage Sorry unexpected argument given "123", expected an array that each element will be used for building a ViewModel named "Rimma" in the collection
	 */
	public function testExceptionShouldBeThrownAtRegisterOfCollectionOfViewModelsByInvalidFittingDataNotArray_Int()
	{
		$this->repo->collectionAddRimma(123);
	}

	/**
	 * @expectedException InvalidArgumentException
	 * @expectedExceptionMessage Sorry unexpected argument given "123.456", expected an array that each element will be used for building a ViewModel named "Rimma" in the collection
	 */
	public function testExceptionShouldBeThrownAtRegisterOfCollectionOfViewModelsByInvalidFittingDataNotArray_Real()
	{
		$this->repo->collectionAddRimma(123.456);
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
	 * @expectedExceptionMessage Invalid method called "ViewModelServiceTest\TestAsset\ViewModelRepo::createBestEverViewModel()", for adding or getting ViewModel(s) only allowed "add<ViewModel>()" or "get<ViewModel>()"
	 */
	public function testShouldThrowExceptionAtTryingToWorkWithMagicalMethodCallOnAddDataToRepoOrByGetViewModelFromRepo()
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
	 * @expectedExceptionMessage Singleton un serializing is an invalid approach
	 */
	public function testShouldTrowExceptionAtWakeUp()
	{
		$string = serialize($this->repo);
		unserialize($string);
	}
}
