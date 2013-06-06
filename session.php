<?php

require_once('lib/SessionHandler.php');

$session = new SessionHandler();

// add db data
$session->setDbDetails('localhost', 'session', '@session312', 'ws_sessions');

// OR alternatively send a MySQLi ressource
// $session->setDbConnection($mysqli);

$session->setDbTable('session_handler_table');
session_set_save_handler(array($session, 'open'),
array($session, 'close'),
array($session, 'read'),
array($session, 'write'),
array($session, 'destroy'),
array($session, 'gc'));
session_start();

if (!isset($_SESSION['safety']))
{
	session_regenerate_id(true);
	$_SESSION['safety'] = true;
}
$_SESSION['sessionid'] = session_id();
?>