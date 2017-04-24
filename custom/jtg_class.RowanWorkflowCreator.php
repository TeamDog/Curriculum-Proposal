<?php

/*
 * This is the sloppiest, messiest, last minute code again. Needs to be cleaned up and documented. - jtg
 * I will avenge your code style -Nick
 */
class RowanWorkflowCreator {
	public static function createWorkflow($author, $processType, $department, $college, $dms) {

		ini_set('display_errors', 1);
		ini_set('display_startup_errors', 1);
		error_reporting(E_ALL);
		
		//MySQL Connection Information
		$servername = "localhost";
		$username = "rowancpms";
		$password = "rowancpms";
		$dbname = "rowan_cpms";
		// Create connection
		$conn = new mysqli($servername, $username, $password, $dbname);
		$seeddmsConn = new mysqli($servername, $username, $password, "seeddms");
		// Check connection
		if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
		}
		if ($seeddmsConn->connect_error) {
		die("Connection failed: " . $seeddmsConn->connect_error);
		}
		
		//sql to get name of department
		$sql = "SELECT seeddms.tblGroups.name 
			FROM seeddms.tblGroups
			WHERE seeddms.tblGroups.id = \"".$department."\" AND seeddms.tblGroups.group_level = \"DEP\";"; //use AND group_level check to ensure non-department groups won't be found in case of invalid id
			
		//execute query
		$depNameResult = $seeddmsConn->query($sql);
		$depName = "";
		
		if ($depNameResult->num_rows > 0) {
			// output data of each row
			while($row = $depNameResult->fetch_assoc()) {
				$depName = $row["name"];
			}
		} else {
			var_dump($department);
			echo "Didn't find department ".$department." in database.";
		}
		
		//sql to get name of department
		$sql = "SELECT seeddms.tblGroups.name 
			FROM seeddms.tblGroups
			WHERE seeddms.tblGroups.id = \"".$college."\" AND seeddms.tblGroups.group_level = \"COL\";"; //use AND group_level check to ensure non-department groups won't be found in case of invalid id
			
		//execute query
		$colNameResult = $seeddmsConn->query($sql);
		$colName = "";
		
		if ($colNameResult->num_rows > 0) {
			// output data of each row
			while($row = $colNameResult->fetch_assoc()) {
				$colName = $row["name"];
			}
		} else {
			echo "Didn't find college ".$college." in database.";
		}
		
		
		//take out all spaces/whitespace from department and college names
		$colName = trim($colName);
		$depName = trim($depName);
		
		//init vars
		$seeddmsWorkflowName = $depName . " Process " . $processType.time(); //concat all info to make a unique workflow name.
		echo "The workflow name is: ".$seeddmsWorkflowName;
		
		
		$rowanWorkflowID = -1;
		$rowanInitialNodeID = -1;
		$rowanNodeArray = array();
		
		
		
		/**************
			Query to get the workflow template ID using the process type
				
		***************/
		$sql = "SELECT rowan_workflows.id_rowan_workflows 
			FROM rowan_workflows
			WHERE rowan_workflows.process_type = \"".$processType."\";"; // the dot does string concat
			
		//execute query
		$result = $conn->query($sql);
		
		if ($result->num_rows > 0) {
			// output data of each row
			while($row = $result->fetch_assoc()) {
				$rowanWorkflowID = $row["id_rowan_workflows"];
			}
		} else {
			echo "0 results";
		}
		
		
		/**************	
		Query to get the intial node ID using the WF id
		***************/
		//set query
		$sql = "SELECT rowan_workflows.initial_node 
			FROM rowan_workflows
			WHERE rowan_workflows.id_rowan_workflows = \"".$rowanWorkflowID."\";"; // the dot does string concat
			
		//execute query
		$result = $conn->query($sql);

		if ($result->num_rows > 0) {
			// output data of each row
			while($row = $result->fetch_assoc()) {
				$rowanInitialNodeID = $row["initial_node"];
			}
		} else {
			echo "0 results";
		}
		
		
		
		/**************
			Query to get all nodes (their IDs) associated with a given worklfow id
				
		***************/
		
		$sql = "SELECT rowan_nodes.id_rowan_nodes
			FROM rowan_nodes
			WHERE rowan_nodes.associated_workflow = \"".$rowanWorkflowID."\";"; // the dot does string concat
			
		//execute query
		$result = $conn->query($sql);
		
		if ($result->num_rows > 0) {
			// output data of each row
			
			while($row = $result->fetch_assoc()) {
				array_push($rowanNodeArray, $row["id_rowan_nodes"]);
			}
		} else {
			echo "0 results";
		}
		
		//declare var to save seeddms node ID for initial workflow node
		$seeddmsInitialNodeID = -1;
		$seeddmsStateArray = array();
		
		
		
		//***********make nodes *************
		foreach ($rowanNodeArray as $rowanNode)
		{
			//get doc status
			$docstatus = "?";
			$sql = "SELECT rowan_nodes.document_state
			FROM rowan_nodes
			WHERE rowan_nodes.id_rowan_nodes = \"".$rowanNode."\";"; // the dot does string concat
			
			//execute query
			$result = $conn->query($sql);
			
			if ($result->num_rows > 0) {
				// output data of each row
				
				while($row = $result->fetch_assoc()) {
					$docstatus = $row["document_state"];
				}
			} else {
				echo "0 results";
			}
			
			
			//get group
			$rowanNodeGroup = "";
			$sql = "SELECT rowan_nodes.group, rowan_nodes.name
			FROM rowan_nodes
			WHERE rowan_nodes.id_rowan_nodes = \"".$rowanNode."\";"; // the dot does string concat
			
			//execute query
			$result = $conn->query($sql);
			$stateDesc = "";
			if ($result->num_rows > 0) {
				// output data of each row
				
				while($row = $result->fetch_assoc()) {
					$rowanNodeGroup = $row["group"];
					$stateDesc = $row["name"];
				}
			} else {
				echo "0 results";
			}
			
			//Get the rowanNodeGroup's name and group level
			//sql to get name of department
			$sql = "SELECT seeddms.tblGroups.name, seeddms.tblGroups.group_level 
				FROM seeddms.tblGroups
				WHERE seeddms.tblGroups.id = \"".$rowanNodeGroup."\";"; //use AND group_level check to ensure non-department groups won't be found in case of invalid id
				
			//execute query
			$groupResult = $seeddmsConn->query($sql);
			$groupLevel = "";
			
			if ($groupResult->num_rows > 0) {
				// output data of each row
				while($row = $groupResult->fetch_assoc()) {
					$groupLevel = $row["group_level"];
				}
			}
			
			$stateName = "ErrorInNameGeneration";
			switch($groupLevel) {
				case "DEP":
					//if department level, intersect with input department
					$stateName = $depName." ".$stateDesc.time();
					break;
				case "COL":
					//if college level, intersect with input college
					$stateName = $colName." ".$stateDesc.time();
					break;
				case "UNI";
					//if university level, just use input group
					$stateName = $stateDesc.time();
					break;
				default:
					//make the array empty by default
					$stateName = $stateDesc.time();
					break;
			}
			
			
			echo "The state name is: ".$stateName;
			$newWorkflowstate = $dms->addWorkflowState($stateName, $docstatus);
			if ($rowanNode == $rowanInitialNodeID)
			{
				$seeddmsInitialNodeID = $newWorkflowstate;
			}
			
			$seeddmsStateArray[ (string) $rowanNode] = $newWorkflowstate;
		}
		
		
		
		
		
		//Create seeddms WF obj instance
		$seeddmsWorkflowObject = $dms->addWorkflow($seeddmsWorkflowName, $seeddmsInitialNodeID);
		
		
		
		
		//*******add transitions************
		//get approve transition object
		$approveActionObj = $dms->getWorkflowAction(6); //6 is ID in DB, bad practice but this is last min bullshit again
		//get reject transisiton object
		$rejectActionObj = $dms->getWorkflowAction(2); //7 is ID in DB, bad practice but this is last min bullshit again
		
		foreach ($rowanNodeArray as $rowanNode)
		{
			//check approve field
			$rowanNodeApprove = "";
			$rowanNodeReject = "";
			$sql = "SELECT rowan_nodes.node_approve, rowan_nodes.node_reject, rowan_nodes.group
			FROM rowan_nodes
			WHERE rowan_nodes.id_rowan_nodes = \"".$rowanNode."\";"; // the dot does string concat
			
			//execute query
			$result = $conn->query($sql);
			
			if ($result->num_rows > 0) {
				// output data of each row
				
				while($row = $result->fetch_assoc()) {
					$rowanNodeApprove = $row["node_approve"];
					$rowanNodeReject = $row["node_reject"];
					$rowanNodeGroup =  $row["group"];
				}
			}
			
			
			//USER ARRAY CALCULATION (BASED ON THE GROUP LEVEL FOR THE CURRENT NODE: department, college, university)
			$sql = "SELECT seeddms.tblGroups.group_level
			FROM seeddms.tblGroups
			WHERE id = \"".$rowanNodeGroup."\";";
			
			$groupLevel = "";
			//execute query
			$result = $seeddmsConn->query($sql);
			
			
			if ($result->num_rows > 0) {
				// output data of each row
				while($row = $result->fetch_assoc()) {
					$groupLevel = $row["group_level"];
				}
			} else {
				echo "<br>",$rowanNode," in rowan_nodes doesnt return any results for its group's group_level";
			}
			$arrayOfUserIDs = null;
			//based on the level of the group, we want to find the user array differently
			switch($groupLevel) {
				case "DEP":
					//if department level, intersect with input department
					$arrayOfUserIDs = RowanWorkflowCreator::findUsersInGroups(array($rowanNodeGroup, $department), $seeddmsConn);
					break;
				case "COL":
					//if college level, intersect with input college
					$arrayOfUserIDs = RowanWorkflowCreator::findUsersInGroups(array($rowanNodeGroup, $college), $seeddmsConn);
					break;
				case "UNI";
					//if university level, just use input group
					$arrayOfUserIDs = RowanWorkflowCreator::findUsersInGroups(array($rowanNodeGroup), $seeddmsConn);
					break;
				default:
					//make the array empty by default
					$arrayOfUserIDs = array();
					break;
			}

			$arrayOfUsers = array();
			foreach ($arrayOfUserIDs as $userID)
			{
				array_push($arrayOfUsers, $dms->getUser($userID));
			}
			var_dump($rowanNodeApprove);
			var_dump($rowanNodeReject);
			if ($rowanNodeApprove != "" && !empty($rowanNodeApprove) && $rowanNodeApprove != "0") //if not null, add transition 
			{
				$seeddmsWorkflowObject->addTransition($seeddmsStateArray[ (string) $rowanNode], $approveActionObj, $seeddmsStateArray[ (string) $rowanNodeApprove], $arrayOfUsers, array());
			}
			
			//do same for reject
			if ($rowanNodeReject != "" && !empty($rowanNodeReject) && $rowanNodeReject != "0") //if not null, add transition 
			{
				$seeddmsWorkflowObject->addTransition($seeddmsStateArray[ (string) $rowanNode], $rejectActionObj, $seeddmsStateArray[(string) $rowanNodeReject], $arrayOfUsers, array());
			
			}
			
			
		}
		
		$conn->close();
		$seeddmsConn->close();
		
		return $seeddmsWorkflowObject;

		

	}
	
	private static function findUsersInGroups($arrayOfGroupIDs, $dbConnection)
	{
		$sql = "SELECT userID FROM seeddms.tblGroupMembers WHERE groupID = \"".$arrayOfGroupIDs[0]."\"";
		
		if (sizeof($arrayOfGroupIDs) > 1)
		{
			for ($i = 1; $i < sizeof($arrayOfGroupIDs); $i++)
			{
				$sql = $sql."AND userID IN (SELECT userID FROM seeddms.tblGroupMembers WHERE groupID = \"".$arrayOfGroupIDs[$i]."\")";
			}
		}
		
		$sql = $sql.";";
		
		
		$userResult = array();
			
			
		//execute query
		$result = $dbConnection->query($sql);
		
		if ($result->num_rows > 0) {
			// output data of each row
			
			while($row = $result->fetch_assoc()) {
				array_push($userResult, $row["userID"]);
			}
		}
		
		return $userResult;
	}
}


?>