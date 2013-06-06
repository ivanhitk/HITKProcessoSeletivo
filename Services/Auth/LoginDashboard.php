<?php
ini_set("session.use_cookies", 1);
//ini_set("session.use_only_cookies", 1);
ini_set("session.use_trans_sid", 1);

//require_once('../../lib/dbSession.inc.php');
require_once('../../lib/sessiondb.inc.php');

if ( isset($_REQUEST[htmlentities(session_name())]) )
{
	session_id($_REQUEST[htmlentities(session_name())]);
}

session_start();

if ( isset( $_REQUEST['logout']) )
{
	session_destroy();
	if ( isset($_REQUEST['return']) )
		header("Location: " .$_REQUEST['return']);
	else
		header("Location: http://services.hitk.com.br");
	die();
}

switch ($_SERVER['HTTP_ORIGIN'])
{
	case 'http://reports.hitk.com.br': case 'https://reports.hitk.com.br':
		header('Access-Control-Allow-Origin: '.$_SERVER['HTTP_ORIGIN']);
		header('Access-Control-Allow-Methods: POST, OPTIONS');
		header('Access-Control-Max-Age: 1000');
		header('Access-Control-Allow-Headers: Content-Type');
		/* header('Access-Control-Allow-Headers: origin, x-requested-with, Content-Type'); */
		break;
}

/* if ( isset($_POST['email']) && isset($_POST['password'])) { */
if ( isset($_REQUEST['email']) && isset($_REQUEST['password'])) {

	include('../../lib/edb/edb.class.php');

	/* Connect to an ODBC database using driver invocation
	 $dsn = 'mysql:dbname=wellclinic_ic;host=mysql.hitk.com.br';
	*/
	$host = 'mysql.hitk.com.br';
	$user = 'ivanadm';
	$password = 'sjml123';
	$database = 'hitk_sso_prd';

	$db = new edb($host,$user,$password,$database);
	@$db->q("SET character_set_results = 'utf8', character_set_client = 'utf8', character_set_connection = 'utf8', character_set_database = 'utf8', character_set_server = 'utf8'");

	$sql = "SELECT email, name, code FROM hitk_sso_prd.profile as p WHERE p.password = AES_ENCRYPT('" .$_REQUEST['password']. "','@Hitk.....3321') AND email = '" .$_REQUEST['email']. "'";

	$result = $db->line($sql);

	if ( ! is_null($result['code']) )
	{

		//print 'Entrei 2 ...';
		//ini_set("session.use_cookies", 1);
		//ini_set("session.use_only_cookies", 1);
		//ini_set("session.use_trans_sid", 1);


		$_SESSION['access_token'] = $result['code'];
		$_SESSION['name'] = $result['name'];
		$_SESSION['email'] = $result['email'];

		/* Autenticação OK */
		$arr = array('ReplyCode' => 1, 'SID' => session_id());
		print json_encode($arr);
	} else {
		/* Autenticação Falhou */
		$arr = array('ReplaCode' => 0, 'Origin' => $_SERVER['HTTP_ORIGIN']);
		print json_encode($arr);
	}


} else {
	/* Autenticação Falhou = email e senha enviado incorreto */
	$arr = array('ReplaCode' => 2, 'Origin' => $_SERVER['HTTP_ORIGIN']);
	print json_encode($arr);
}

?>