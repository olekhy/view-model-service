<?php
namespace ViewModelServiceTest\TestAsset\ViewModel;

use ViewModelService\ViewModel\ViewModelInterface;

class FooViewModel implements ViewModelInterface
{
	public function __construct()
	{
		$this->input = func_get_args();
	}

	public function getBuildArgs()
	{
		return $this->input;
	}
}
