<?php
/**
 * Implementation of ViewFolder view
 *
 * @category   DMS
 * @package    SeedDMS
 * @license    GPL 2
 * @version    @version@
 * @author     Uwe Steinmann <uwe@steinmann.cx>
 * @copyright  Copyright (C) 2002-2005 Markus Westphal,
 *             2006-2008 Malcolm Cowe, 2010 Matteo Lucarelli,
 *             2010-2012 Uwe Steinmann
 * @version    Release: @package_version@
 */

/**
 * Include parent class
 */
require_once("class.Bootstrap.php");

/**
 * Include class to preview documents
 */
//require_once("SeedDMS/Preview.php");

/**
 * Class which outputs the html page for ViewFolder view
 *
 * @category   DMS
 * @package    SeedDMS
 * @author     Markus Westphal, Malcolm Cowe, Uwe Steinmann <uwe@steinmann.cx>
 * @copyright  Copyright (C) 2002-2005 Markus Westphal,
 *             2006-2008 Malcolm Cowe, 2010 Matteo Lucarelli,
 *             2010-2012 Uwe Steinmann
 * @version    Release: @package_version@
 */
class SeedDMS_View_RowanTEMPLATE extends SeedDMS_Bootstrap_Style {

	function show() { /* {{{ */
		$dms = $this->params['dms'];
		$user = $this->params['user'];
		
		//$version = $this->params['version'];
		//$availversions = $this->params['availversions'];

		$this->htmlStartPage();
		$this->globalNavigation();
		$this->contentStart();
		$this->pageNavigation("Browse");
		
		echo "<div class=\"row-fluid\">\n";
		
		echo "<div class=\"span4\">\n";
		$this->contentContainerStart();
		echo "The filter buttons";
		$this->contentContainerEnd();
		echo "</div>\n"; //span4
		
		
		
		//***********BEGIN BROWSE AREA *******************
		echo "<div class=\"span8\">\n";
		$this->contentContainerStart();
	
		echo "Browse area";
		
		
		$this->contentContainerEnd();
		echo "</div>\n"; //span8
		
		//***********END BROWSE AREA *******************
		
		echo "</div>\n"; //row-fluid
		
		$this->contentEnd();
		$this->htmlEndPage();
	} /* }}} */
}

?>
