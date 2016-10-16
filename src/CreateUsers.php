<?php

#require_once "bootstrap.php"
include 'User.php';
echo "Passed user name :" . $argv[1];
$newUserName = $argv[1];


$user = new User();
$user->setUserName($newUserName);

$entityManager->persist($user);
$entityManager->flush();

echo "Created user with id:" . $user->getUserId() . "\n";
