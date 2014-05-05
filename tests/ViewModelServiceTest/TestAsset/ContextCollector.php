<?php
namespace ViewModelServiceTest\TestAsset;

class ContextCollector
{
	/**
	 * @var array of class names with methods
	 */
	protected $collection;

	/**
	 * @return mixed
	 */
	public function getCollection()
	{
		return $this->collection;
	}

	public function append($value)
	{
		$this->collection[] = $value;
	}

}
