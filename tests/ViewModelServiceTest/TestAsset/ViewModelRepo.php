<?php
/**
 * Created by PhpStorm.
 * User: al
 * Date: 6/9/14
 * Time: 2:59 PM
 */

namespace ViewModelServiceTest\TestAsset;


use ViewModelService\ViewModelRepo;
use ViewModelServiceTest\TestAsset\ViewModel\FooViewModel;
use ViewModelServiceTest\TestAsset\ViewModel\RimmaViewModel;
use ViewModelServiceTest\TestAsset\ViewModel\SuperViewModel;

/**
 * Class ViewRepo
 *
 * @package ViewModelServiceTest\TestAsset
 *
 * @method FooViewModel getFoo($optionalPostfix = null)
 * @method SuperViewModel getSuper($optionalPostfix = null)
 * @method RimmaViewModel getRimma($optionalPostfix = null)
 */
class ViewRepo extends ViewModelRepo
{

}
