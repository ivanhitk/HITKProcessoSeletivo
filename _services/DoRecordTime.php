<?php
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', dirname(__FILE__) . '/error_log.txt');
error_reporting(E_ALL);
ini_set("session.use_cookies", 1);
//ini_set("session.use_only_cookies", 1);
ini_set("session.use_trans_sid", 1);

//require_once('../lib/dbSession.inc.php');
require_once('../lib/sessiondb.inc.php');

if ( isset($_REQUEST[htmlentities(session_name())]) )
{
	session_id($_REQUEST[htmlentities(session_name())]);
}

session_start();

$host = 'localhost';
$user = 'hitkprocess';
$password = '@hitkprocess312';
$dbname = 'HITKProcess';
$date = new DateTime();
$date->setTimezone(new DateTimeZone('America/Sao_Paulo'));

try {

	$db = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);
	$db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );

	/*** INSERT data ***/
	@$db->exec("SET character_set_results = 'utf8', character_set_client = 'utf8', character_set_connection = 'utf8', character_set_database = 'utf8', character_set_server = 'utf8'");
	$UpdateStartProof = "UPDATE HITKProcess.Candidato as c SET ";
	$UpdateStartProof .= "	LastTime = '" .$_REQUEST['Time']. "'";
	$UpdateStartProof .= " WHERE c.email = '" .$_SESSION['Email']. "' AND";
	$UpdateStartProof .= " c.Token = '" .$_SESSION['Token']. "'";
		
	$DoUpdate = $db->exec($UpdateStartProof);
}
catch(PDOException $e) {
	echo $e->getMessage();
	die();
}

?>