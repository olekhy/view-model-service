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
class SuperViewMapper extends AbstractViewMapper
{
	/**
	 * @param mixed $data
	 * @throws \Exception
	 * @return TestAssetViewModel
	 */
	public function map($data)
	{
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
