<?
include("mysql_sessions.inc");

session_set_save_handler("mysql_session_open", "mysql_session_close",
"mysql_session_select", "mysql_session_write",
"mysql_session_destroy",
"mysql_session_garbage_collect");

//session_start();

// At this point, sessions can be used just as they were in the
// previous article!

?>