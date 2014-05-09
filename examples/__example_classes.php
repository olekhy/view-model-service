<?php


use ViewModelService\ViewModel\ViewModelInterface;
use ViewModelService\ViewMapper\AbstractViewMapper;
use ViewModelService\ViewModelRepo;






// ###############################################################################################
// ####################################### UserViewModel #########################################
// ###############################################################################################

class UserViewModel implements ViewModelInterface
{
	public $username;
	public $email;

	public function __construct($options)
	{
		$this->email = $options['email'];
		$this->username = $options['username'];
	}
}


// ###############################################################################################
// #################################### UserSpecificViewModel ####################################
// ###############################################################################################

class UserSpecificViewModel implements ViewModelInterface
{
	public $username;
	public $email;


}

// ###############################################################################################
// ####################################### UserViewMapper #########################################
// ###############################################################################################

class UserSpecificViewMapper extends  AbstractViewMapper
{
	/**
	 * @return void
	 */
	public function map()
	{
		$data = $this->getDataForMapping();
		foreach ($data as $name => $item)
		{
			$this->model->$name = strtoupper($item);
		}
	}

}









// ###############################################################################################
// ################################ UserWithFriendsViewModel #####################################
// ###############################################################################################



/**
 * Class UserWithFriendsViewModel
 */
class UserWithFriendsViewModel implements ViewModelInterface
{
	/**
	 * @var UserViewModel
	 */
	public $user;
	/**
	 * @var array of ids in repo
	 */
	public $friendsViewModelIdsInRepo;

	public $friendsModels;

	public function __construct($options)
	{
		$this->user = $options['user'];
		$this->friendsViewModelIdsInRepo = $options['friends'];
	}

	/**
	 * @return UserViewModel[]
	 */
	public function getFriendsViewModels()
	{
		if (null === $this->friendsModels)
		{
			$this->friendsModels = ExampleViewModelRepo::getRepo()->collectionGetUser($this->friendsViewModelIdsInRepo);
		}
		return $this->friendsModels;
	}
}







// ###############################################################################################
// ################################ ExampleViewModelRepo #####################################
// ###############################################################################################

/**
 * Class ExampleViewModelRepo
 *
 * @method string                   addUserWithFriends($dataAware, $optionalId = null)
 * @method UserWithFriendsViewModel getUserWithFriends()
 * @method string                   addUser($dataAware, $optionalId = null)
 * @method UserViewModel            getUser()
 * @method string                   addUserSpecific($dataAware, $optionalId = null)
 * @method UserSpecificViewModel    getUserSpecific()
 * @method mixed                    collectionAddUser(array $usersDataCollection);
 * @method ViewModelInterface[]     collectionGetUser(array $viewModelRepoIds);
 */
class ExampleViewModelRepo extends ViewModelRepo
{

}

