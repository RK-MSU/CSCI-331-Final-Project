<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// ExpressionEngine Config Items
// Find more configs and overrides at
// https://docs.expressionengine.com/latest/general/system-configuration-overrides.html

$config['app_version'] = '6.1.3';
$config['encryption_key'] = '60e2da06aa211d4f68028a0a0eae0b360d817f76';
$config['session_crypt_key'] = 'd80329d1e3a430cb1f2746fa412a5c56ed07070a';
$config['database'] = array(
	'expressionengine' => array(
		'hostname' => 'localhost',
		'database' => 'db_name',
		'username' => 'db_username',
		'password' => 'db_password',
		'dbprefix' => 'exp_',
		'char_set' => 'utf8mb4',
		'dbcollat' => 'utf8mb4_unicode_ci',
		'port'     => ''
	),
);
$config['show_ee_news'] = 'y';

// EOF
