<?php

use ViewModelService\ViewModelComposer;

require __DIR__ . '/__autoloader.php';
require __DIR__ . '/__example_classes.php';


$repo = ExampleViewModelRepo::getRepo();
$repo->setViewModelComposer(new ViewModelComposer(array('namespace' => false)));

// data from foreign resource fe database
$data = array(
	array('username' => 'Monika', 'email' => 'monika@gmail.com'),
	array('username' => 'Klara', 'email' => 'klara@gmail.com'),
	array('username' => 'Rose', 'email' => 'rose@gmail.com'),
);

$viewModelIds = $repo->collectionAddUser($data);

$repo->addUserWithFriends(
	array(
		'user' => $repo->addUser(123),
		'friends' => $viewModelIds
	)
);

$friends = $repo->getUserWithFriends();

var_dump($friends->getFriendsViewModels());

