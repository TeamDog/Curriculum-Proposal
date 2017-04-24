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


if (isset($_GET["rowanWorkflowID"]) && is_numeric($_GET["rowanWorkflowID"]) ) {
	$rowanWorkflowID=$_GET["rowanWorkflowID"];
	
	if($view ) {
	
	//$view->setParam('version', $v);
	//$view->setParam('availversions', $versions);
	$view->setParam('rowanWorkflowID', $rowanWorkflowID);
	$view($_GET);
	exit;
	}
} else {
	echo "Invalid Process ID";
}


?>