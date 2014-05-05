<?php
namespace ViewModelServiceTest\TestAsset\ViewModel;

use ViewModelService\ViewModel\ViewModelInterface;

class RimmaViewModel implements  ViewModelInterface
{
	public $att_1;
	public $att_2;
	public $att_3;

	/**
	 * @param null $data
	 */
	public function __construct($data = null)
	{
		 if (!empty($data))
		 {
				$this->att_1 = key($data) . current($data);
				next($data);
				$this->att_2 = key($data) . current($data);
				next($data);
			 	$this->att_3 = key($data) . current($data);
		 }
	}
}
