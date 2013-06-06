<?php
require_once('sessiondb.class.php');

//ini_set('session.gc_probability', 50);
ini_set('session.save_handler', 'user');

define('SESSION_DB_HOST','mysql.hitk.com.br');
define('SESSION_DB_USER','ivanadm');
define('SESSION_DB_PASS','sjml123');
define('SESSION_DB_DATABASE','hitk_session_prd');

$session = new Session();
session_set_save_handler(array($session, 'open'),
array($session, 'close'),
array($session, 'read'),
array($session, 'write'),
array($session, 'destroy'),
array($session, 'gc'));

// below sample main

session_start();
//session_regenerate_id(true);

?>