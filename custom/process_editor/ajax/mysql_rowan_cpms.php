<?php
/*
 * function to create a DB connection to rowan_cpms
 * Author: Justin Gavin (jtg)
 * 		http://justingavin.com/
 */

function mysql_rowan_cpms () {

	// Create connection
	$rowanConn = new mysqli("localhost", "rowancpms", "rowancpms", "rowan_cpms");
	// Check connection
	if ($rowanConn->connect_error) {
		die("Connection failed: " . $rowanConn->connect_error);
	}
	return $rowanConn;
}

?>