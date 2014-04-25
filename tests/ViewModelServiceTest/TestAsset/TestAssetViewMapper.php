<?php
namespace ViewModelServiceTest\TestAsset;

use Exception;
use ViewmodelService\ViewMapper\AbstractViewMapper;

/**
 * Class TestAssetViewMapper
 *
 * @package AutoTest\ViewMapperService\TestAsset
 */
class TestAssetViewMapper extends AbstractViewMapper
{
	public function map()
	{
		$data = call_user_func($this->dataAware);
		foreach ($this->model as $key => $var)
		{
			if (isset($data[$key]))
			{
				$this->$key = $data[$key];
			}
			else
			{
				throw new Exception('Can not map data to view');
			}
		}
	}
}
