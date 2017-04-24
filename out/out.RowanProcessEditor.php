<?php
/*
 * Author: Justin Gavin (jtg)
 * 		http://justingavin.com/
 * Spring 2017
 */
include("../inc/inc.Settings.php");
include("../inc/inc.Language.php");
include("../inc/inc.Init.php");
include("../inc/inc.Extension.php");
include("../inc/inc.DBInit.php");
include("../inc/inc.ClassUI.php");
include("../inc/inc.Authentication.php");




$tmp = explode('.', basename($_SERVER['SCRIPT_FILENAME']));
$view = UI::factory($theme, $tmp[1], array('dms'=>$dms, 'user'=>$user));



if($view) {
	
	$view($_GET);
	exit;
}
?>