<?php
$DSN = 'mysql:host=localhost;dbname=ecommerce';
$login = 'root';
$mdp = '';
$options = array(
	PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING,
	PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES UTF8');
$pdo = new PDO($DSN, $login, $mdp, $options);