<?php

ini_set('error_reporting', -1);

// installed itself
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
	require_once __DIR__ . '/../vendor/autoload.php';

// installed as dependency
}

die('DD');
