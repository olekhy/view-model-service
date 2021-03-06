<?php

use ViewModelService\ViewModelComposer;

require __DIR__ . '/__autoloader.php';
require __DIR__ . '/__example_classes.php';

/** @var ExampleViewModelRepo $repo */
$repo = ExampleViewModelRepo::getRepo();
$repo->setViewModelComposer(new ViewModelComposer(array('namespace' => false)));

// data from foreign resource fe database
$callable = function()
{
	return array('username' => 'Monika', 'email' => 'monika@gmail.com');
};

$repo->addUser($callable);

var_dump($repo->getUser());

