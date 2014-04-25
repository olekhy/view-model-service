View Model service
==================


The module named "ViewModelService" could be used for:

 - Applying the data obtained from database or user input to the instance of the view model  with the aid appropriate mapper.
 - Obtaining view model that contains data.
 - Convenience of the Autocompleting for the  view models in the view template.


#Explaining of the process

At first we have many View Models classes which are in principe a simple presentation of variables that we want use in our view templates.
All of the View Model classes needed to be named via convention <ConcreteName>ViewModel and must implement an interface ViewModelInterface.
Parallel to the view models we use View Model Mapper classes, the purpose of the mapper class is the mapping data to the View Model instance.
Naming convention for the Mapper class is similar to the View Model naming and and looks like this <ConcreteName>ViewMapper.
All mappers must realizing the map() method which contains logic that describes how will be mapped Data to the View Model.

Process of applying of data is internally segregated into two logical independent processes,
first is the recording in the repository a recipe which describes how can be view model created and second is the creation a real view model instance.
The purpose of this one segregation ist performance of execution of the request as simple as we can say the View Model Instance will be created
only by the necessary requirement.

Next we explains how we get the work with the View model Service

As for example we are at Request in UserController::actionShowProfile()

```php

use ViewModelService\ViewModelRepo;

class UserController
{

	function actionShowProfile()
	{
		$self = $this;

		ViewModelRepo::getRepo()->addUserProfile(function() use($self)
		{
			$userId = $self->getRequest()->getParam('userId');
			return $self->getUserService()->findUserProfile($userId);
		);});
	}

	public function getUserService()
	{
		return new UserService();
	}
}

```

In View Template

```php

	echo ViewModelRepo::getRepo()->addUserProfile()->firstname;
	echo '<br/>';
	echo ViewModelRepo::getRepo()->addUserProfile()->lastname;

```

Important is that the both UserProfileViewModel and UserProfileViewMapper classes exists in the code scope.

Follow you can see same oter cases of the using:

```php
// output immediately as json
echo json_decode(ViewModelRepo::getRepo()->addConcreteViewModel($callable)->getConcreteMode());

// define creation of the view model with an specific identifier
// this is necessary for creation more than one view model instance are contains different data
ViewModelRepo::getRepo()->getConcreteViewModel(array(1,2,3), 'bbbb');
ViewModelRepo::getRepo()->getConcreteViewModel(new stdClass, 'aaaa');

ViewModelRepo::getRepo()->getConcreteViewModel();
ViewModelRepo::getRepo()->getConcreteViewModel('bbbb');
ViewModelRepo::getRepo()->getConcreteViewModel('aaaa');

```
