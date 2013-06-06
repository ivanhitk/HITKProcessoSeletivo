<?php

require_once 'Services/Auth/HITKProcessAuth.inc.php';

if ( $AuthReply['ReplyCode'] == 0 )
{
	include_once 'tlp/Main.inc.php';
} else {
	include_once 'tlp/Main.Auth.Error.php';
}

?>