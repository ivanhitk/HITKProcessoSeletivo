<?php

// Session Table

$sess_table = "SessionsTable";
$sess_db = mysql_pconnect("localhost", "session", "@session312") or die("Database error");
// Retrieve the session maximum lifetime (found in php.ini)

$lifetime = (ini_get('session.gc_maxlifetime') * 60) * 3600;
//=============
// function: mysql_session_open()
// purpose: Opens a persistent server connection and selects the
//    database.
//=============

function mysql_session_open($session_path, $session_name) {

	mysql_pconnect("localhost", "session", "@session312")
	or die("Can't connect to MySQL server! ");

	mysql_select_db("hitk_session_prd")
	or die("Can't select MySQL sessions database");

} // end mysql_session_open()

//=============
// function: mysql_session_close()
// purpose: Doesn't actually do anything since the server connection is
//    persistent. Keep in mind that although this function
//    doesn't do anything in my particular implementation, I
//    still must define it.
//=============

function mysql_session_close() {

	return 1;

} // end mysql_session_close()

//=============
// function: mysql_session_select()
// purpose: Reads the session data from the database
//=============

function mysql_session_select($SID) {

	GLOBAL $sess_db;
	GLOBAL $sess_table;

	$query = "SELECT value FROM $sess_table
	WHERE SID = '$SID' AND
	expiration > ". time();

	$result = mysql_query($query, $sess_db);
	if (!$result)
		print 'Deu merda no banco parceiro! => Tentei acessar a tabela de sessões com o SID informado, não trouxe os valores corretos';

	$row = mysql_fetch_array($result);

	return $row["value"];
} // end mysql_session_select()

//=============
// function: mysql_session_write()
// purpose: This function writes the session data to the database. If that SID // already exists, then the existing data will be updated.
//=============

function mysql_session_write($SID, $value) {

	GLOBAL $sess_db;
	GLOBAL $sess_table;
	GLOBAL $lifetime;

	$expiration = time() + $lifetime;

	$query = "SELECT value FROM $sess_table
	WHERE SID = '{$SID}' AND
	expiration > ". time();

	if (!($result = @ mysql_query($query, $sess_db)))
		print 'Deu merda no banco parceiro! => Tentei acessar a tabela de sessões com o SID informado, não executou a query';

	if(mysql_num_rows($result) == 0)
	{
		// Debug
		//echo "Entrei INSERT -";
		$query = "INSERT INTO $sess_table
		VALUES('$SID', '$expiration', '$value')";

		$result = mysql_query($query, $sess_db);

		if (!$result)
			print 'Deu merda no banco parceiro! => Tentei inserir os dados de sessão com o SID informado, não executou a query não, da uma olhada lá';
	} else {
		//echo "Entrei INSERT - UPDATE";
		$query = "UPDATE $sess_table SET ";
		$query .= "	expiration = '$expiration'";
		if ( $value )
			$query .= "	, value = '$value' ";
		$query .= "	WHERE SID = '$SID' AND expiration >". time();

		$result = mysql_query($query, $sess_db);
		if (!$result)
			print 'Deu merda no banco parceiro! => Tentei atualizar os dados de sessÃ£o com o SID informado, nÃ£o executou a query nÃ£o, da uma olhada lÃ¡';

	}
	/*
	 $query = "INSERT INTO $sess_table
	VALUES('$SID', '$expiration', '$value')";

	$result = mysql_query($query, $sess_db);


	if (! $result) :

	$query = "UPDATE $sess_table SET
	expiration = '$expiration',
	value = '$value' WHERE
	SID = '$SID' AND expiration >". time();

	$result = mysql_query($query, $sess_db);

	endif;
	*/


} // end mysql_session_write()

//=============
// function: mysql_session_destroy()
// purpose: deletes all session information having input SID (only one row)
//=============

function mysql_session_destroy($sessionID) {

	GLOBAL $sess_table;

	$query = "DELETE FROM $sess_table
	WHERE SID = '$sessionID'";
	$result = mysql_query($query);

} // end mysql_session_destroy()

//=============
// function: mysql_session_garbage_collect()
// purpose: deletes all sessions that have expired.
//=============

function mysql_session_garbage_collect($lifetime) {

	GLOBAL $sess_table;

	$query = "DELETE FROM $sess_table
	WHERE sess_expiration < ".time() - $lifetime;
	$result = mysql_query($query);

	return mysql_affected_rows($result);

} // end mysql_session_garbage_collect()

?>