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


class SeedDMS_View_RowanProcessEditor_Node extends SeedDMS_Bootstrap_Style {


/****** DB FUNCTIONS ******/	
	
	function execute_Singleton_SQL_Query($DBconn, $query, $col)
	{
		if(empty($DBconn) || empty($query))
			return NULL;
		//sql to get name of department
				
		//execute query
		$result = $DBconn->query($query);
		
		$val = NULL;
		
		if ($result->num_rows == 1) {
			// output data of each row
			$row = $result->fetch_assoc();
			$val = $row[$col];
			
		}
		return $val;
	}

/****** END * DB FUNCTIONS ******/


//Main function, called on display
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
		
		
		
		$rowanWorkflowID = $this->params['rowanWorkflowID'];
		if (empty($rowanWorkflowID))
			die("Invalid Process ID");
		$rowanWorkflowName = $this->execute_Singleton_SQL_query($rowanConn,
																"SELECT rowan_workflows.process_type
																FROM rowan_cpms.rowan_workflows
																WHERE rowan_workflows.id_rowan_workflows = \"".$rowanWorkflowID."\";"
																,"process_type");
		
		//Doc state select dropdown
		//Prefix with <select id="PUT ID HERE"
		$documentStateSelect = '
			name="document_state">
				<option value="0">Keep</option>
				<option value="2">Release</option>
				<option value="-1">Reject</option>
			</select>
		';
		
		$documentStateArray = array("0"=>"Keep","2"=>"Release","-1"=>"Reject");
		
		
		//Group select dropdown
		$sql = 'SELECT id, name FROM seeddms.tblGroups WHERE is_role="1";';
		$sqlResult = $rowanConn->query($sql);
		$groupSelectList = "";
		$groupArray = array();
		if ($sqlResult->num_rows > 0) {
			// output data of each row
			while($row = $sqlResult->fetch_assoc()) {				
				$groupSelectList = $groupSelectList.'<option value="'.$row["id"].'">'.$row["name"].'</option>';
				$groupArray[$row["id"]] = $row["name"];
			}
		} else {
			
		}	
		$groupSelectList = $groupSelectList.'</select>';
		
		
		
		
		/***** SEEDDMS STUFF*****/
		$dms = $this->params['dms'];
		$user = $this->params['user'];
		
		//Shove something into header 'css' for CSS 'js' for javascript
		$this->htmlAddHeader('<script type="text/javascript" src="../custom/process_editor/js/node_javascript.js"></script>'."\n", 'js');
		
		$this->htmlAddHeader('<link rel="stylesheet" href="../custom/styles/w3.css"><link href="../custom/process_editor/css/process_editor_styles.css" rel="stylesheet">'."\n", 'css');
		
		/***** END * SEEDDMS STUFF*****/
		
		
	
		
		
		//$version = $this->params['version'];
		//$availversions = $this->params['availversions'];
		
		
		$this->htmlStartPage();
		$this->globalNavigation();
		$this->contentStart();
		$this->pageNavigation("Editing Process: <b>".$rowanWorkflowName."</b>");
			
		echo "<div class=\"row-fluid\">\n";
        
		
		echo "<div class=\"span12\">\n";
		$this->contentContainerStart();
		//***********BEGIN BROWSE AREA *******************
		
		
		echo '<button class="w3-button w3-lightgrey w3-border w3-margin-bottom" id="changeTypeButton">Change Process Type Name</button>';
		
		
		echo '<table class="w3-table-all w3-hoverable">
				<thead>
				<tr class="w3-light-grey">
					<th>Node ID</th>
					<th>Description</th>
					<th>Associated Group</th>
					<th>Document State</th>
					<th>Approve to: [ID]</th>
					<th>Reject to: [ID]</th>
					
				</tr>
				</thead>';
			
			
		
		$sql = "SELECT * FROM rowan_cpms.rowan_nodes WHERE rowan_nodes.associated_workflow=\"".$rowanWorkflowID. "\";";
			
		//Loop once to build table/selection list
		$sqlResult = $rowanConn->query($sql);
		
		$nodeSelectList = "";
		if ($sqlResult->num_rows > 0) {
			// output data of each row
			while($row = $sqlResult->fetch_assoc()) {				
				echo '<tr class="clickable-row" nodeID="'.$row["id_rowan_nodes"].'">';
				echo "<td>".$row["id_rowan_nodes"]."</td>";
				echo "<td>".$row["name"]."</td>";
				echo "<td>";
				if (!empty($row["group"]))
					echo $groupArray[$row["group"]];
				echo "</td>";
				echo "<td>";
				if (!empty($row["document_state"]) || $row["document_state"] == 0)
					echo $documentStateArray[$row["document_state"]];
				echo "</td>";
				echo "<td>".$row["node_approve"]."</td>";
				echo "<td>".$row["node_reject"]."</td>";
				echo "</tr>";
				
				$nodeSelectList = $nodeSelectList.'<option value="'.$row["id_rowan_nodes"].'">'.$row["id_rowan_nodes"].': '.$row["name"].'</option>';
			}
			
			
		} else {
			echo "Didn't find any Process Types in database.";
		}	
		$nodeSelectList = $nodeSelectList.'</select>';
		echo "</table>";
		
		
		//Loop again to build editor forms
		$sqlResult = $rowanConn->query($sql);
		$editNodeForms = "";
		if ($sqlResult->num_rows > 0) {
			// output data of each row
			while($row = $sqlResult->fetch_assoc()) {
				$editNodeForms = $editNodeForms.
				'
				<div style="display: none;" id="nodeEditHTML_'.$row["id_rowan_nodes"].'">
					<h4>Editing Node: '.$row["id_rowan_nodes"].'</h4>
					<form id="nodeEditForm_'.$row["id_rowan_nodes"].'" action="../custom/process_editor/ajax/edit_node.php" method="POST">
					Node Description:<br>
					<input id="nodeEditName_'.$row["id_rowan_nodes"].'" type="text" name="name" value="'.$row["name"].'"><br>
					
					Associated Group<br>
					<select id="nodeEditGroup_'.$row["id_rowan_nodes"].'" sel="'.$row["group"].'" >
					<option value="NULL">NONE</option>
					'.$groupSelectList.'<br>
					
					Document State<br>
					<select id="nodeEditDocState_'.$row["id_rowan_nodes"].'" sel="'.$row["document_state"].'" '.$documentStateSelect.'<br>
					
					Approve to: [ID]<br>					
					<select id="nodeEditApprove_'.$row["id_rowan_nodes"].'" sel="'.$row["node_approve"].'" >
					<option value="NULL">NONE</option>
					'.$nodeSelectList.'<br>
					
					Reject to: [ID]<br>					
					<select id="nodeEditReject_'.$row["id_rowan_nodes"].'" sel="'.$row["node_reject"].'" >
					<option value="NULL">NONE</option>
					'.$nodeSelectList.'<br>
					
					<input class="w3-button w3-lightgrey w3-border w3-margin-top" type="submit" value="Submit">
					</form>
				
				
				</div>
			';
				

			}
			
			
		} else {
			
		}	
		
		
		
		
		
		
		/***** Buttons ******/	
		echo '<button class="w3-button w3-lightgrey w3-border w3-margin" id="newNodeButton">New Node</button>';
		echo '<button class="w3-button w3-lightgrey w3-border w3-margin" id="delNodeButton">Delete Node</button>';
		echo '<br><a class="w3-button w3-grey w3-border w3-margin" href="out.RowanProcessEditor.php"> ◄ Back to Process List </a>';
		/***** END * Buttons ******/	
		
		
		/***** Modal HTML ******/	
		echo '<div id="pe-gen-modal" class="pe-modal">
			<div class="pe-modal-box">
				<span id="pe-gen-close" class="pe-close">&times;</span>';
		
			/***** Change Type HTML ******/
			echo '
				<div style="display: none;" id="changeTypeHTML">
					<h4>Change Type</h4>
					<form id="changeTypeForm" action="../custom/process_editor/ajax/change_process_name.php" method="POST">
					Process Type Name:<br>
					<input id="changeTypeHTML_new_type" type="text" name="new_type" value="'.$rowanWorkflowName.'"><br>
					<input class="w3-button w3-lightgrey w3-border w3-margin-top" type="submit" value="Submit">
					</form>
				
				
				</div>
			';
			/***** END * Change Type HTML ******/
			
			/***** New Node HTML ******/
			echo '
				<div style="display: none;" id="newNodeHTML">
					<h4>New Node</h4>
					<form id="newNodeForm" action="../custom/process_editor/ajax/add_node.php" method="POST">
					Node Description:<br>
					<input id="newNodeHTML_name" type="text" name="name"><br>
					Proposal State:<br>
					<select id="newNodeHTML_document_state"'.$documentStateSelect.'<br>
					<input class="w3-button w3-lightgrey w3-border w3-margin-top" type="submit" value="Submit">
					</form>
				
				
				</div>
			';
			/***** END * New Node HTML ******/
			
			
			/***** Del Node HTML ******/
			echo '
				<div style="display: none;" id="delNodeHTML">
				<h4>Delete Node</h4>
				<form id="delNodeForm" action="../custom/process_editor/ajax/del_node.php" method="POST">
					Node to Delete:<br>
					<select id="delNodeHTML_node_id" name="rowanNodeID">'.$nodeSelectList.'<br>
					<input class="w3-button w3-lightgrey w3-border w3-margin-top" type="submit" value="Submit">
					</form>
				</div>
			';
			/***** END * Del Node HTML ******/
				
				
			/***** Node Edit HTML ******/	
			echo $editNodeForms;
			/***** END *  Node Edit HTML ******/
				
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
