<?php
namespace ViewModelServiceTest\TestAsset;


use ViewModelService\ViewModelRepo as Repo;
use ViewModelServiceTest\TestAsset\ViewModel\FooViewModel;
use ViewModelServiceTest\TestAsset\ViewModel\RimmaViewModel;
use ViewModelServiceTest\TestAsset\ViewModel\SuperViewModel;

/**
 * Class ViewModelRepo
 *
 * @package ViewModelServiceTest\TestAsset
 *
 * - ! - View Model Register
 * @method string addFoo(mixed $data)
 * @method string addSuper(mixed $array)
 *
 * @method FooViewModel getFoo($optionalPostfix = null)
 * @method SuperViewModel getSuper($optionalPostfix = null)
 * @method RimmaViewModel getRimma($optionalPostfix = null)
 *
 * - ! - Collections register
 *
 * @method  collectionAddRimma()
 *
 * - ! - Collection pick up
 *
 * @method SuperViewModel[] collectionGetSuper($optionalPostfix = null)
 * @method RimmaViewModel[] collectionGetRimma($optionalPostfix = null)
 * @method FooViewModel[]   collectionGetFoo($optionalPostfix = null)
 *
 *
 * - ! - Bad named method
 * @method createBestEverViewModel()
 *
 *
 */
class ViewModelRepo extends Repo
{
}
