<?php
namespace ViewModelServiceTest;

use BadMethodCallException;
use InvalidArgumentException;
use LogicException;
use PHPUnit_Framework_TestCase as TestCase;
use SebastianBergmann\Exporter\Exception;
use ViewModelService\ViewModelComposer;
use ViewModelServiceTest\TestAsset\ViewModel\FooViewModel;
use ViewModelServiceTest\TestAsset\ViewModelRepo;
use ViewModelServiceTest\TestAsset\ViewModel\RimmaViewModel;

class ViewModelRepo_special_Test extends TestCase
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


	public function test_view_models_registered_by_same_way_are_not_identical_by_calling_them_by_using_returned_id()
	{
		$id1 = $this->repo->addFoo(1);
		$id2 = $this->repo->addFoo(2);
		$id3 = $this->repo->addFoo(3);
		$id4A = $this->repo->addFoo(4, 'A');
		$id5B = $this->repo->addFoo(5, 'B');
		$ids6A = $this->repo->addSuper(6, 'A');
		$ids7B = $this->repo->addSuper(7, 'B');

		$this->assertSame('Foo', $id1);
		$this->assertSame($id4A, 'FooA');
		$this->assertSame($id5B, 'FooB');
		$this->assertSame($ids6A, 'SuperA');
		$this->assertSame($ids7B, 'SuperB');

		$this->assertNotEquals('Foo', $id2);
		$this->assertNotEquals('Foo', $id3);
		$this->assertNotEquals('Foo', $id4A);
		$this->assertNotEquals('Foo', $id5B);
		$this->assertNotEquals($id1, $id2);
		$this->assertNotEquals($id1, $id3);
		$this->assertNotEquals($id1, $id4A);
		$this->assertNotEquals($id1, $id5B);

		$this->assertNotEquals($id2, $id3);
		$this->assertNotEquals($id2, $id4A);
		$this->assertNotEquals($id2, $id5B);

		$this->assertNotEquals($id3, $id4A);
		$this->assertNotEquals($id3, $id5B);
		$this->assertNotEquals($id4A, $id5B);

		$this->assertSame(1, $this->repo->getFoo($id1)->input[0]);

		$this->assertSame(1, $this->repo->getFoo()->input[0]);


		$this->assertSame(2, $this->repo->getFoo($id2)->input[0]);

		$this->assertSame(3, $this->repo->getFoo($id3)->input[0]);

		$this->assertSame(4, $this->repo->getFoo($id4A)->input[0]);

		$this->assertSame(4, $this->repo->getFoo('A')->input[0]);

		$this->assertSame(5, $this->repo->getFoo($id5B)->input[0]);

		$this->assertSame(5, $this->repo->getFoo('B')->input[0]);

	}

	public function test_a()
	{
		$idFooCollection = $this->repo->registerModelsCollection('Foo', array(1));
		$idFoo = $this->repo->addFoo(array(2));
		$idFoo1 = $this->repo->addFoo(array(3));
		$idFooCollection1 = $this->repo->registerModelsCollection('Foo', array(10,20,30,40));

		$this->assertSame('Foocollection', $idFooCollection);
		$this->assertSame('Foo', $idFoo);
		$this->assertNotEquals('Foo', $idFoo1);
		$this->assertNotEquals($idFoo1, $idFooCollection);
		$this->assertSame('Foocollection1', $idFooCollection1);
	}

	public function test_b()
	{
		$gqty = 23;  // look bottom for "// $gqty ..."
		$resDataFoo = function($data) { return array_map(function($v){return $v = array('input' => array($v));}, $data); };
		$inDataFoo = function($prefix, $qty = 10) use (&$gqty) { $gqty +=$qty; return array_fill(0, $qty , uniqid($prefix));};
		$resDataRimma = function($data) { return array_map(function($v) { return $v = array('att_1' => '0'.array_shift($v), 'att_2' => '', 'att_3' => '' );},$data); };
		$inDataRimma = function($prefix, $qty = 10) use (&$gqty) { $gqty +=$qty; return array_fill(0, $qty, array(uniqid($prefix)));};

		$dataFoo1 = $inDataFoo('data_foo_1', 7);
		$dataFoo2 = $inDataFoo('data_foo_2', 10);
		$dataFoo3 = $inDataFoo('data_foo_3', 25);

		$dataRimma1 = $inDataRimma('data_rimma_1', 12);
		$dataRimma2 = $inDataRimma('data_rimma_2', 21);
		$dataRimma3 = $inDataRimma('data_rimma_3', 11);

		$dataFooA1 = $inDataFoo('data_foo_A1', 9);
		$dataFooB2 = $inDataFoo('data_foo_B2', 19);
		$dataFooC3 = $inDataFoo('data_foo_B3', 15);

		$dataRimmaA1 = $inDataRimma('data_rimma_A1', 11);
		$dataRimmaB2 = $inDataRimma('data_rimma_B2', 22);
		$dataRimmaC3 = $inDataRimma('data_rimma_C3', 16);

		$stack = array(
			$this->repo->addFoo('a'),                       // $gqty++
			$this->repo->addFoo('a', 'A'),                  // $gqty++

			$this->repo->addRimma(array('rimma_1')),        // $gqty++
			$this->repo->addRimma(array('rimma_2'), 'A'),   // $gqty++

			$this->repo->registerModelsCollection('Foo', $dataFoo1),
			$this->repo->registerModelsCollection('Foo', $dataFoo2),
			$this->repo->registerModelsCollection('Foo', $dataFoo3),

			$this->repo->registerModelsCollection('Rimma', $dataRimma1),
			$this->repo->registerModelsCollection('Rimma', $dataRimma2),
			$this->repo->registerModelsCollection('Rimma', $dataRimma3),

			$this->repo->registerModelsCollection('Foo', $dataFooA1, 'A'),
			$this->repo->registerModelsCollection('Foo', $dataFooB2, 'B'),
			$this->repo->registerModelsCollection('Foo', $dataFooC3, 'C'),

			$this->repo->registerModelsCollection('Rimma', $dataRimmaA1, 'A'),
			$this->repo->registerModelsCollection('Rimma', $dataRimmaB2, 'B'),
			$this->repo->registerModelsCollection('Rimma', $dataRimmaC3, 'C'),
		);

		$ids = array(
				'Foo'              => array('type' => 'Foo',   'expectation' => array(array('input' => array('a')))),
				'FooA'             => array('type' => 'Foo',   'expectation' => array(array('input' => array('a')))),
				'Rimma'            => array('type' => 'Rimma', 'expectation' => array(array('att_1' => '0rimma_1', 'att_2' => '', 'att_3' => ''))),
				'RimmaA'           => array('type' => 'Rimma', 'expectation' => array(array('att_1' => '0rimma_2', 'att_2' => '', 'att_3' => ''))),
				'Foocollection'    => array('type' => 'Foo',   'expectation' => $resDataFoo($dataFoo1)),
				'Foocollection1'   => array('type' => 'Foo',   'expectation' => $resDataFoo($dataFoo2)),
				'Foocollection2'   => array('type' => 'Foo',   'expectation' => $resDataFoo($dataFoo3)),
				'Rimmacollection'  => array('type' => 'Rimma', 'expectation' => $resDataRimma($dataRimma1)),
				'Rimmacollection4' => array('type' => 'Rimma', 'expectation' => $resDataRimma($dataRimma2)),
				'Rimmacollection5' => array('type' => 'Rimma', 'expectation' => $resDataRimma($dataRimma3)),
				'FooAcollection'   => array('type' => 'Foo',   'expectation' => $resDataFoo($dataFooA1)),
				'FooBcollection'   => array('type' => 'Foo',   'expectation' => $resDataFoo($dataFooB2)),
				'FooCcollection'   => array('type' => 'Foo',   'expectation' => $resDataFoo($dataFooC3)),
				'RimmaAcollection' => array('type' => 'Rimma', 'expectation' => $resDataRimma($dataRimmaA1)),
				'RimmaBcollection' => array('type' => 'Rimma', 'expectation' => $resDataRimma($dataRimmaB2)),
				'RimmaCcollection' => array('type' => 'Rimma', 'expectation' => $resDataRimma($dataRimmaC3)),
		);

		$expectation = implode(', ', array_keys($ids));

		$this->assertSame($expectation, implode(', ', $stack));

		$examine = function(array $models, $type, $expectation) use(&$gqty)
		{
			foreach ($models as $item)
			{
				$gqty--;
				$this->assertInstanceOf('ViewModelServiceTest\TestAsset\ViewModel\\' . $type . 'ViewModel', $item);
				$this->assertSame(array_shift($expectation), get_object_vars($item));
			}
		};

		$models = $this->repo->collectionGetFoo();
		$examine($models, 'Foo', $resDataFoo($dataFoo1));        // $gqty += 7

		$models = $this->repo->collectionGetRimma();
		$examine($models, 'Rimma', $resDataRimma($dataRimma1));  // $gqty += 12

		foreach ($ids as $id => $element)
		{
			if (strpos($id, 'collection') !== false)
			{
				$methodName = 'collectionGet' . $element['type'];
				$argument = $id;

				if (false === strpos($id, $element['type'] . 'collection')) {
					 	$argument = substr(substr($id, strlen($element['type'])), 0, -(strlen($id) - strpos($id, 'collection')));
				}
				$models = $this->repo->$methodName($argument);
			}
			else
			{
				$methodName = 'get' . $element['type'];
				$argument = null;
				if(strlen($id) > strlen($element['type']))
				{
					$argument = substr($id, strlen($element['type']));
				}

				$model = $this->repo->$methodName($argument);
				$models = array($model);
			}
			//echo "$methodName($argument)\n";
			$examine($models, $element['type'], $element['expectation']);
		}
		$this->assertSame(0, $gqty);
	}
}
