<?php
/*
 * Author: Justin Gavin (jtg)
 * 		http://justingavin.com/
 * Spring 2017
 */

/**
 * Include parent class
 */
require_once("class.Bootstrap.php");


class SeedDMS_View_RowanProcessEditor extends SeedDMS_Bootstrap_Style {
	
	//MySQL DB Related Vars
		var $rowanConn;
		var $seeddmsConn;	
	
	
	//"Main" function, called on display
	function show() { /* {{{ */
		
		
		/***** INIT DB CONNECTIONS *****/
		$servername = "localhost";	
		$username = "rowancpms";
		$password = "rowancpms";
		
		
		// Create connection
		$rowanConn = new mysqli($servername, $username, $password, "rowan_cpms");
		$seeddmsConn = new mysqli($servername, $username, $password, "seeddms");
		// Check connection
		if ($rowanConn->connect_error) {
		die("Connection failed: " . $rowanConn->connect_error);
		}
		if ($seeddmsConn->connect_error) {
		die("Connection failed: " . $seeddmsConn->connect_error);
		}
		
		/***** END * INIT DB CONNECTIONS *****/
		
		//SeedDMS Stuff
		$dms = $this->params['dms'];
		$user = $this->params['user'];
		
		
		//$viewType = $this->params['rowanViewType'];
		
		
		//$version = $this->params['version'];
		//$availversions = $this->params['availversions'];
		
		//How to shove something into header 'css' for CSS 'js' for javascript
		
		$this->htmlAddHeader('<script type="text/javascript" src="../custom/process_editor/js/workflow_javascript.js"></script>'."\n", 'js');
		$this->htmlAddHeader('<link rel="stylesheet" href="../custom/styles/w3.css"><link href="../custom/process_editor/css/process_editor_styles.css" rel="stylesheet">'."\n", 'css');
		
		$this->htmlStartPage();
		$this->globalNavigation();
		$this->contentStart();
		
		$this->pageNavigation("Process Type Listing");
		
			
			
		echo "<div class=\"row-fluid\">\n";
        
		
		echo "<div class=\"span12\">\n";
		$this->contentContainerStart();
		//***********BEGIN BROWSE AREA *******************
		
		echo '<table class="w3-table-all w3-hoverable">
				<thead>
				<tr class="w3-light-grey">
					<th >Process Type</th>
		  
				</tr>
				</thead>';
			
			
		//sql to get name of department
		$sql = "SELECT rowan_workflows.id_rowan_workflows, rowan_workflows.process_type FROM rowan_cpms.rowan_workflows;";
			
		//execute query
		$sqlResult = $rowanConn->query($sql);
		
		$selectList = '<select name="rowanWorkflowID">';
		if ($sqlResult->num_rows > 0) {
			// output data of each row
			while($row = $sqlResult->fetch_assoc()) {
				echo "<tr>";
				echo "<td><a href=\"out.RowanProcessEditor_Node.php?rowanWorkflowID=".$row["id_rowan_workflows"]."\" style=\"display:block;\">".$row["process_type"]."</a></td>";
				echo "</tr>";
				
				$selectList = $selectList.'<option value="'.$row["id_rowan_workflows"].'">'.$row["process_type"].'</option>';
			}
		} else {
			echo "Didn't find any Process Types in database.";
		}	
		$selectList = $selectList.'</select>';
		echo "</table>";
		
		/***** Buttons ******/	
		echo '<button class="w3-button w3-lightgrey w3-border w3-margin" id="newProcessButton">New Process</button>';
		echo '<button class="w3-button w3-lightgrey w3-border w3-margin" id="delProcessButton">Delete Process</button>';
		/***** END * Buttons ******/	
		
		
		/***** Modal HTML ******/	
		echo '<div id="pe-gen-modal" class="pe-modal">
			<div class="pe-modal-box">
				<span id="pe-gen-close" class="pe-close">&times;</span>';
		
			/***** New Process HTML ******/
			echo '
				<div style="display: none;" id="newProcessHTML">
					<h4>New Process</h4>
					<form id="newProcessForm" action="../custom/process_editor/ajax/add_process.php" method="POST">
					Process Type:<br>
					<input type="text" name="process_type"><br>
					<input class="w3-button w3-lightgrey w3-border w3-margin-top" type="submit" value="Submit">
					</form>
				
				
				</div>
			';
			/***** END * New Process HTML ******/
			
			
			/***** Del Process HTML ******/
			echo '
				<div style="display: none;" id="delProcessHTML">
				<h4>Delete Process</h4>
				<form id="delProcessForm" action="../custom/process_editor/ajax/del_process.php" method="POST">
					Process Type to Delete:<br>
					'.$selectList.'<br>
					<input class="w3-button w3-lightgrey w3-border w3-margin-top" type="submit" value="Submit">
					</form>
				</div>
			';
			/***** END * Del Process HTML ******/
					
				
				
		echo '</div>';
		/***** END * Modal HTML ******/	
		
		
		
		
		
		//***********END BROWSE AREA *******************
		$this->contentContainerEnd();
		echo "</div>\n"; //span8
		
		echo "</div>\n"; //row-fluid
		
		$this->contentEnd();
		$this->htmlEndPage();
		
		
		//KILL DB CONNECTIONS
		if (!empty($rowanConn))
			$rowanConn->close();
		if (!empty($seeddmsConn))
			$seeddmsConn->close();
	} /* }}} */
}

?>
