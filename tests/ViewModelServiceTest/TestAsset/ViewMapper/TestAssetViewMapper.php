<?php
namespace ViewModelServiceTest\TestAsset\ViewMapper;

use Exception;
use ViewModelService\ViewMapper\AbstractViewMapper;
use ViewModelServiceTest\TestAsset\ViewModel\TestAssetViewModel;

/**
 * Class TestAssetViewMapper
 *
 * @package ViewMapperService\TestAsset
 */
class TestAssetViewMapper extends AbstractViewMapper
{
	/**
	 * @return TestAssetViewModel
	 * @throws Exception
	 */
	public function map()
	{
		$data = call_user_func($this->dataAware);
		foreach ($this->model as $key => $var)
		{
			if (isset($data[$key]))
			{
				$this->model->{$key} = $data[$key];
			}
			else
			{
				throw new Exception('Can not map data to view');
			}
		}
		return $this->model;
	}
}
