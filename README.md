View Model service
==================

[![Build Status](https://travis-ci.org/olekhy/view-model-service.svg)](https://travis-ci.org/olekhy/view-model-service)

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/olekhy/view-model-service/badges/quality-score.png?s=35a91a0db76d2b4f3c638c48a26e25f6f2015336)](https://scrutinizer-ci.com/g/olekhy/view-model-service/)

[![Coverage Status](https://coveralls.io/repos/olekhy/view-model-service/badge.png?branch=develop)](https://coveralls.io/r/olekhy/view-model-service?branch=master)

[![Coverage Status](https://coveralls.io/repos/olekhy/view-model-service/badge.png?branch=develop)](https://coveralls.io/r/olekhy/view-model-service?branch=develop)


The module named "ViewModelService" could be used for:

 - Inserting data obtained from database or user's input in the instance of the View Model via appropriate mapper.
 - Latter inserting (applying) data to the View Model and building an instance at the time when View Model was called in template.
 - Convenience at coding in IDE from the autocomplete for the View Models in the view template.
 - Flexible and secure approach of the logic how data will be mapped to the View Model (Using of mappers).
 - Lazy loading data in view model via Repository without using of controller.


#Explaining of the process and root idea

Firstly we will have in codebase of project many View Models classes which oneself a simples presentation of variables
that we obtained from database or another sources and will be using in our view templates.
All of the View Model classes needed to be named by convention **ConcreteNameViewModel** and must implement an interface **ViewModelInterface**.

Parallel to the view models we create View Model Mapper classes, the purpose of the mapper class is the mapping data to the View Model instance.
Naming convention for the Mapper class is similar to the View Model naming and and looks like this **ConcreteNameViewMapper**.
All mappers implement an interface **ViewMapperInterface** and must realizing the map() method which contains logic that describes
how will be mapped data to the View Model.

Process of applying of data is internally segregated into two logical independent processes,
first is the recording in the repository a recipe which describes how can the View Model created and second is creation of a real View Model instance.
The purpose of this segregation is performance of execution of the request as simple as we can say the View Model Instance will be created
only at calling also on requirement in the template.

Next we explain how we could work with the View Model Service

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

now in View Template

```php

	echo ViewModelRepo::getRepo()->getUserProfile()->firstname;
	echo '<br/>';
	echo ViewModelRepo::getRepo()->getUserProfile()->lastname;

```

Important is that the both UserProfileViewModel and UserProfileViewMapper classes exists in the code scope.

Follow you can see some other usage cases:

```php

// output immediately as json
echo json_encode(ViewModelRepo::getRepo()->addConcreteViewModel($callable)->getConcreteMode());

// creation of the view model with an specific identifier
// this is necessary for creation more than one view model instance that contains different data
ViewModelRepo::getRepo()->addConcreteViewModel(array('status_first' => 1, 'status_second' => 2, 'level' => 3), 'id_concrete_1');

ViewModelRepo::getRepo()->addConcreteViewModel(new stdClass, 'id_concrete_2');

// get data in template
echo ViewModelRepo::getRepo()->getConcreteViewModel();
// ...
echo ViewModelRepo::getRepo()->getConcreteViewModel('id_concrete_1')->status_first;
// ...
echo ViewModelRepo::getRepo()->getConcreteViewModel('id_concrete_2');

```
