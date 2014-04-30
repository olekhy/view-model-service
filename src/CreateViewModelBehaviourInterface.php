<?php
namespace ViewModelService;

interface CreateViewModelBehaviourInterface
{
	/**
	 * @return mixed
	 */
	public function on();

	/**
	 * @return mixed
	 */
	public function off();
}
