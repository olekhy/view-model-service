<?php
namespace ViewModelServiceTest\TestAsset\ViewMapper;

use Exception;
use ViewModelService\ViewMapper\AbstractViewMapper;
use ViewModelServiceTest\TestAsset\ViewModel\SuperViewModel;
use ViewModelServiceTest\TestAsset\ViewModel\TestAssetViewModel;

/**
 * Class SuperViewMapper
 *
 * @package ViewMapperService\TestAsset
 */
class SuperViewMapper extends AbstractViewMapper
{
	/**
	 * @throws Exception
	 * @return SuperViewModel
	 */
	public function map()
	{
		$data = $this->getDataForMapping();
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
