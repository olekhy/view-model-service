<?php
namespace ViewModelServiceTest\TestAsset;

use ViewModelService\AbstractContextualCreate;

class ContextB extends AbstractContextualCreate
{
	protected $collector;

	function __construct(ContextCollector $collector)
	{
		$this->collector = $collector;
	}

	/**
	 * @return mixed
	 */
	public function setUpContext()
	{
		$this->collector->append(__METHOD__);
	}

	/**
	 * @return mixed
	 */
	public function closeContext()
	{
		$this->collector->append(__METHOD__);
	}

}
