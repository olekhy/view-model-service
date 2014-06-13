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
	public function test_Should_Throw_Exception_If_Invalid_Recipe()
	{
		$view = $this->repo->getSuper();
	}

	public function test_Register_View_Model_By_Name_And_Get_Same_ViewModel_As_Given_At_Register()
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

	public function test_Should_Register_Collection_Of_View_Models_And_Getting_Of_The_Same_Collection()
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

	public function test_Should_Get_The_Same_Collection_By_Using_Of_Optional_Collection_Name_AddOn()
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
	 *
	 * @expectedException InvalidArgumentException
	 * @expectedExceptionMessage Recipe can not be attached because does not known about handling with less than one or more than two arguments
	 */
	public function test_Add_View_Model_Should_Thrown_Exception_By_Invalid_Arguments_Count_Less_Than_One()
	{

			$this->repo->addFoo();
	}

	/**
	 * @expectedException InvalidArgumentException
	 * @expectedExceptionMessage Recipe can not be attached because does not known about handling with less than one or more than two arguments
	 */
	public function test_Add_View_Model_Should_Thrown_Exception_By_Invalid_Arguments_Count_More_Than_Two()
	{
			$this->repo->addFoo(array(1,2,3), 'name-add-on', 'unexpected argument');
	}

	/**
	 * @expectedException PHPUnit_Framework_Error
	 * @expectedExceptionMessage Undefined index: Foo
	 */
	public function test_Can_Nor_Get_The_Collection_By_Using_Of_Bad_Optional_Collection_Name_AddOn()
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
	public function test_Exception_Should_Be_Thrown_At_Register_Of_Collection_Of_View_Models_By_Invalid_Fitting_Data_Not_Array_Empty()
	{
		$this->repo->collectionAddRimma();
	}

	/**
	 * @expectedException InvalidArgumentException
	 * @expectedExceptionMessage Sorry unexpected argument given "stdClass::__set_state(array(
	))", expected an array that each element will be used for building a ViewModel named "Rimma" in the collection
	 */
	public function test_Exception_Should_Be_Thrown_At_Register_Of_Collection_Of_View_Models_By_Invalid_Fitting_Data_Not_Array_Not_Traversable_Instance()
	{
		$this->repo->collectionAddRimma(new \stdClass);
	}

	/**
	 * @expectedException InvalidArgumentException
	 * @expectedExceptionMessage Sorry unexpected argument given "NULL", expected an array that each element will be used for building a ViewModel named "Rimma" in the collection
	 */
	public function test_Exception_Should_Be_Thrown_At_Register_Of_Collection_Of_View_Models_By_Invalid_Fitting_Data_Not_Array_Null()
	{
		$this->repo->collectionAddRimma(null);
	}

	/**
	 * @expectedException InvalidArgumentException
	 * @expectedExceptionMessage Sorry unexpected argument given "NULL", expected an array that each element will be used for building a ViewModel named "Rimma" in the collection
	 */
	public function test_Exception_Should_Be_Thrown_At_Register_Of_Collection_Of_View_Models_By_Invalid_Fitting_Data_Not_Array_String()
	{
		$this->repo->collectionAddRimma('');
	}

	/**
	 * @expectedException InvalidArgumentException
	 * @expectedExceptionMessage Sorry unexpected argument given "123", expected an array that each element will be used for building a ViewModel named "Rimma" in the collection
	 */
	public function test_Exception_Should_Be_Thrown_At_Register_Of_Collection_Of_View_Models_By_Invalid_Fitting_Data_Not_Array_Int()
	{
		$this->repo->collectionAddRimma(123);
	}

	/**
	 * @expectedException InvalidArgumentException
	 * @expectedExceptionMessage Sorry unexpected argument given "123.456", expected an array that each element will be used for building a ViewModel named "Rimma" in the collection
	 */
	public function test_Exception_Should_Be_Thrown_At_Register_Of_Collection_Of_View_Models_By_Invalid_Fitting_Data_Not_Array_Real()
	{
		$this->repo->collectionAddRimma(123.456);
	}

	/**
	 * @expectedException LogicException
	 * @expectedExceptionMessage You try to override already registered recipe named "Superin_test"
	 */
	public function test_Should_Throw_Exception_If_Recipe_Was_Added_Twice()
	{
		$this->repo->addSuper(array(1), 'in_test');
		$this->repo->addSuper(array(2), 'in_test');
	}

	public function test_Should_Create_Multiple_View_Models_By_Same_Typ_And_Different_Content()
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
	public function test_Should_Throw_Exception_At_Trying_To_Work_With_Magical_Method_Call_On_Add_Data_To_Repo_Or_By_Get_View_Model_From_Repo()
	{
			$this->repo->createBestEverViewModel();
	}

	public function test_Has_Always_Composer()
	{
		ViewModelRepo::resetRepo();
		$this->assertInstanceOf('ViewModelService\ViewModelComposer', 	ViewModelRepo::getRepo()->getViewModelComposer());
	}

	/**
	 * @expectedException LogicException
	 * @expectedExceptionMessage Singleton is not cloneable
	 */
	public function test_Should_Trow_Exception_At_Cloning()
	{
		$r1 = clone $this->repo;
	}

	/**
	 * @expectedException LogicException
	 * @expectedExceptionMessage Singleton un serializing is an invalid approach
	 */
	public function test_should_trow_exception_at_wakeup()
	{
		$string = serialize($this->repo);
		unserialize($string);
	}


	public function test_should_return_id_identically_with_the_view_model_name_only_for_first_registered_model()
	{
		$ids = array();
		for ($i = 0;$i < 10; $i++)
		{
			$ids[$i] = $this->repo->registerModel('Foo', uniqid());

			if (0 === $i)
			{
				$this->assertSame('Foo', $ids[$i]);
			}
			else
			{
				$this->assertTrue('Foo' != $ids[$i]);
			}
		}

		$fact = count(array_unique(array_values($ids)));
		$this->assertSame(10, $fact);
	}

  public function test_view_models_are_identical_with_first_registered_by_calling_they_many_times_without_any_optional_name()
	{
		$this->repo->registerModel('Foo', 1);
		$this->repo->registerModel('Foo', 123);

		$foo = $this->repo->getFoo();
		$this->assertInstanceOf('ViewModelServiceTest\TestAsset\ViewModel\FooViewModel', $foo);
		$this->assertSame(array(1), $foo->getBuildArgs());

		$this->assertSame($foo, $this->repo->getFoo());

	}

	public function test_view_models_registered_by_same_way_are_not_identical_by_calling_them_by_using_returned_id()
	{
		$this->repo->registerModel('Foo', 1);
		$viewId = $this->repo->registerModel('Foo', 1);

		$foo = $this->repo->getFoo();
		$foo2 = $this->repo->getFoo($viewId);
		$this->assertInstanceOf('ViewModelServiceTest\TestAsset\ViewModel\FooViewModel', $foo2);
		$this->assertSame(array(1), $foo2->getBuildArgs());
		$this->assertNotSame($foo, $foo2);
	}

	public function test_should_register_same_qty_of_view_models_to_collection_as_qty_of_elements_in_data()
	{
		$data = array(
			1,2,3,4,5,6,7,8
		);
		$this->repo->registerModelsCollection('Foo', $data);

		$models = $this->repo->collectionGetFoo();


		while($expectation = array_shift($data))
		{
			foreach ($models as $key => $model)
			{
				$this->assertInstanceOf('ViewModelServiceTest\TestAsset\ViewModel\FooViewModel', $model);
				if (array($expectation) === $model->getBuildArgs())
				{
					unset($models[$key]);
				}
			}
		}

		$this->assertTrue(empty($data));
		$this->assertTrue(empty($models));
	}

	/**
	 * @expectedException LogicException
	 * @expectedExceptionMessage You try to override already registered recipe named "Foo0"
	 */
	public function test_should_thrown_exception_when_trying_to_register_same_collection_again()
	{
		$data = array_fill(0, 10, uniqid());
		$this->repo->registerModelsCollection('Foo', $data);
		$this->repo->registerModelsCollection('Foo', $data);
	}
	public function test_collections_that_registered_with_same_data_and_differ_optional_name_should_net_be_same()
	{
		$data = array_fill(0, 10, uniqid());
		$this->repo->registerModelsCollection('Foo', $data);
		$this->repo->registerModelsCollection('Foo', $data, 'AA');

		$models = $this->repo->collectionGetFoo();
		$modelsSame = $this->repo->collectionGetFoo();
		$models2 = $this->repo->collectionGetFoo('AA');

		$ok = array();
		foreach ($models as $item)
		{
			foreach ($modelsSame as $itemSame)
			{
				if ($item === $itemSame)
				{
					$ok[] = 1;
				}
				else
				{
					$ok[] = 0;
				}
			}
		}
		$expectation = array_count_values($ok);
		$this->assertTrue(10 === $expectation[1]);
		$this->assertTrue(90 === $expectation[0]);

		$ok = array();
		foreach ($models as $item)
		{
			foreach ($models2 as $item2)
			{
				if ($item === $item2)
				{
					$ok[] = 1;
				}
				else
				{
					$ok[] = 0;
				}
			}
		}
		$expectation = array_count_values($ok);
		$this->assertArrayNotHasKey(1, $expectation);
		$this->assertTrue(100 === $expectation[0]);

	}

	public function test_collections_are_valid()
	{
		$data = array(
				100,2000,30000,400000,5000000,60000000,7000000000,8000000000
		);

		$this->repo->registerModelsCollection('Foo', $data);
		$this->repo->registerModelsCollection('Foo', $data, 'AA');


		$models = $this->repo->collectionGetFoo();
		$models2 = $this->repo->collectionGetFoo('AA');

		while($expectation = array_shift($data))
		{
			foreach ($models as $key => $model)
			{
				$this->assertInstanceOf('ViewModelServiceTest\TestAsset\ViewModel\FooViewModel', $model);
				if (array($expectation) === $model->getBuildArgs())
				{
					unset($models[$key]);
				}
			}
			foreach ($models2 as $key => $model2)
			{
				$this->assertInstanceOf('ViewModelServiceTest\TestAsset\ViewModel\FooViewModel', $model2);
				if (array($expectation) === $model2->getBuildArgs())
				{
					$this->assertNotSame($expectation, $model2, 'Test fails because same instance of view model in repo was found by differ key.');
				}
				unset($models2[$key]);
			}
		}

		$this->assertTrue(empty($data2));
		$this->assertTrue(empty($models2));
	}

}
