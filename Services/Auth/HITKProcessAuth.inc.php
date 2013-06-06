<?php
/*
 ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', dirname(__FILE__) . '/error_log.txt');
error_reporting(E_ALL);
ini_set("session.use_cookies", 1);
//ini_set("session.use_only_cookies", 1);
ini_set("session.use_trans_sid", 1);
*/

//require_once('../lib/dbSession.inc.php');
require_once('lib/sessiondb.inc.php');

if ( isset($_REQUEST[htmlentities(session_name())]) )
{
	session_id($_REQUEST[htmlentities(session_name())]);
}

session_start();

if (isset($_SESSION['TestStart']) == '' )
{
	session_regenerate_id(true);
	$_SESSION['TestStart'] = true;
}

if ( isset($_REQUEST['token']) && isset($_REQUEST['candidato'])) {

	if ( settype($_REQUEST['token'], "string") )
		if ( strlen($_REQUEST['token']) != 41)
		{
			/* Autenticaчуo Falhou */
			$AuthReply = array('ReplyCode' => 1, 'Origin' => $_SERVER['REQUEST_URI']);
				
		} else {

			include('lib/edb/edb.class.php');

			/* Connect to an ODBC database using driver invocation
			 $dsn = 'mysql:dbname=wellclinic_ic;host=mysql.hitk.com.br';
			*/
			$host = 'localhost';
			$user = 'hitkprocess';
			$password = '@hitkprocess312';
			$database = 'HITKProcess';

			$db = new edb($host,$user,$password,$database);
			@$db->q("SET character_set_results = 'utf8', character_set_client = 'utf8', character_set_connection = 'utf8', character_set_database = 'utf8', character_set_server = 'utf8'");

			//$sql = "SELECT email, name, token FROM HITKProcess.Candidato as c WHERE c.token = AES_ENCRYPT('" .$_REQUEST['password']. "','@Hitk.....3321') AND email = '" .$_REQUEST['email']. "'";
			$sql = "SELECT Nome, Token, reToken, email, Pretensao FROM HITKProcess.Candidato as c WHERE c.token ='" . $_REQUEST['token'] . "' AND c.email = '" .$_REQUEST['candidato']. "'";
				
			$result = $db->line($sql);

			if ( ! is_null($result['Token']) )
			{

				//print 'Entrei 2 ...';
				//ini_set("session.use_cookies", 1);
				//ini_set("session.use_only_cookies", 1);
				//ini_set("session.use_trans_sid", 1);


				$_SESSION['Token'] = $result['Token'];
				$_SESSION['Name'] = $result['Nome'];
				$_SESSION['Email'] = $result['email'];
				$_SESSION['reToken'] = $result['reToken'];
				$_SESSION['TestStart'] = 0;


				/* Autenticaчуo OK */
				$AuthReply = array('ReplyCode' => 0, 'SID' => session_id());

				//print json_encode($AuthReply);
			} else {
				/* Autenticaчуo Falhou */
				$AuthReply = array('ReplyCode' => 1, 'Origin' => $_SERVER['REQUEST_URI']);
				//print json_encode($AuthReply);
			}

		}

} else {
	/* Autenticaчуo Falhou = email e senha enviado incorreto */
	$AuthReply = array('ReplyCode' => 2, 'Origin' => $_SERVER['REQUEST_URI']);
	//print json_encode($AuthReply);
}

?>