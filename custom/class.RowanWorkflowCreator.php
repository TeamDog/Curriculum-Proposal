<?php
include("inc.ClassDMS.php");
include("../inc/inc.Settings.php");
include("../inc/inc.LogInit.php");
include("../inc/inc.Utils.php");
include("../inc/inc.Language.php");
include("../inc/inc.Init.php");
include("../inc/inc.Extension.php");
include("../inc/inc.DBInit.php");
include("../inc/inc.ClassUI.php");
include("../inc/inc.Authentication.php");
include("../inc/inc.ClassPasswordStrength.php");

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$servername = "localhost";
$username = "rowancpms";
$password = "rowancpms";
$dbname = "rowan_cpms";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 
    class RowanWorkflowCreator {
        public static function createWorkflow($author, $processType, $department, $college) {

			$name = $author . "_" . $processType . "_" .  $department . "_" . $college; //concat all info to make a unique workflow name.
			$dms = $this->params['dms']; //initialize $dms

			switch($processType){
				case "A":
					$processType = 1;
					break;
				case "B":
					$processType = 2;
					break;
				case "C":
					$processType = 3;
					break;
				case "D":
					$processType = 4;
					break;
				case "E":
					$processType = 5;
					break;
				case "F":
					$processType = 6;
					break;
				case "Q":
					$processType = 7;
					break;
				
			}
			/**************
			Query to get the intial node ID using the process type
			***************/
			$processType = $processType;
			$sql = "SELECT rowan_workflows.initial_node 
			FROM rowan_workflows
			WHERE rowan_workflows.process_type =".$processType.";"; // the dot does string concat
	
			//execute query
			$result = $conn->query($sql);

			if ($result->num_rows > 0) {
			 // output data of each row
				echo "Initial node from Process Type<br><blockquote>";
				while($row = $result->fetch_assoc()) {
					echo "initial_node: ".$row["initial_node"]."<br>";
				}
				echo "</blockquote>";
			} else {
				echo "0 results";
			}
			//end query
			
			/**************
				Query to get the groups/document_state field from node ID
		
				***************/
				$nodeID = $result;
				//groups field
				$sql = "SELECT rowan_nodes.groups
					FROM rowan_nodes
					WHERE rowan_nodes.id_rowan_nodes =".$nodeID.";"; // the dot does string concat
				//document_state 
				/*
				 $sql = "SELECT rowan_nodes.document_state
				FROM rowan_nodes
				 WHERE rowan_nodes.id_rowan_nodes =".$nodeID.";"; // the dot does string concat
				*/
	
				//execute query
				$result = $conn->query($sql);

				if ($result->num_rows > 0) {
				// output data of each row
					echo "Groups from node id<br><blockquote>";
					while($row = $result->fetch_assoc()) {
						echo "groups: ".$row["groups"]."<br>";
					}
					echo "</blockquote>";
				} else {
					echo "0 results";
				}
			//End Query
			
			$initState = $dms->getWorkflowStateByName($result); //gets the state from dms via the name retrieved in the wf db
			$wf = $dms->addWorkflow($name, $initState);//creates unique workflow in dms with unique name and initial state
			
			
			/**************
			Query to get the workflow template ID using the process type
			This can be used to get all nodes associated with the workflow
			***************/
			$processType = $processType;
			$sql = "SELECT rowan_workflows.id_rowan_workflows 
				FROM rowan_workflows
			    WHERE rowan_workflows.process_type =".$processType.";"; // the dot does string concat
	
			//execute query
			$result = $conn->query($sql);

			if ($result->num_rows > 0) {
			    // output data of each row
				echo "Workflow ID from Process Type<br><blockquote>";
			    while($row = $result->fetch_assoc()) {
			        echo "id_rowan_workflows: ".$row["id_rowan_workflows"]."<br>";
		    }
				echo "</blockquote>";
			} else {
			    echo "0 results";
			}
			//End Query
			
			/**************
			Query to get all nodes (their IDs) assocuated with a given worklfow id
		
			***************/
			$workflowID = $result;
			$sql = "SELECT rowan_nodes.id_rowan_nodes
				FROM rowan_nodes
				WHERE rowan_nodes.associated_workflow =".$workflowID.";"; // the dot does string concat
	
			//execute query
			$result = $conn->query($sql);

			if ($result->num_rows > 0) {
			// output data of each row
				echo "Node IDs from associated workflow id<br><blockquote>";
				while($row = $result->fetch_assoc()) {
					echo "id_rowan_nodes: ".$row["id_rowan_nodes"]."<br>";
				}
				echo "</blockquote>";
			} else {
				echo "0 results";
			}
			//End Query

			$nodes = array();
			while($data = $mysql_fetch_assoc($result)){
				$nodes[] = $data;
			}
			//$nodes = $result; //needs some work. must make nodes and array of all the nodes retrieved from the wf db
			
			foreach ($nodes as $node) { //go through each node and create it's transitions
				/**************
				Query to get the groups/document_state field from node ID
		
				***************/
				$nodeID = $node;
				//groups field
				$sql = "SELECT rowan_nodes.groups
					FROM rowan_nodes
					WHERE rowan_nodes.id_rowan_nodes =".$nodeID.";"; // the dot does string concat
				//document_state 
				/*
				 $sql = "SELECT rowan_nodes.document_state
				FROM rowan_nodes
				 WHERE rowan_nodes.id_rowan_nodes =".$nodeID.";"; // the dot does string concat
				*/
	
				//execute query
				$result = $conn->query($sql);

				if ($result->num_rows > 0) {
				// output data of each row
					echo "Groups from node id<br><blockquote>";
					while($row = $result->fetch_assoc()) {
						echo "groups: ".$row["groups"]."<br>";
					}
					echo "</blockquote>";
				} else {
					echo "0 results";
				}
				//End Query
				
				$group1 = $result;//sets the group name based on the group name pull from wf db
				$state1= $dms->getWorkflowStateByName($group1); //gets the state from dms that will act as the first state in the transition.
				
				/**************
				Query to get the next node (node_reject OR node_approve) from node ID
		
				***************/
				$nodeID = $node;
				//approve field
				$sql = "SELECT rowan_nodes.node_approve
					FROM rowan_nodes
				    WHERE rowan_nodes.id_rowan_nodes =".$nodeID.";"; // the dot does string concat

				$result = $conn->query($sql);

				if ($result->num_rows > 0) {
			    // output data of each row
					echo "Approve/Next node from node id<br><blockquote>";
				    while($row = $result->fetch_assoc()) {
				        echo "node_approve: ".$row["node_approve"]."<br>";
				    }
					echo "</blockquote>";
				} else {
				    echo "0 results";
				}
				//End Query
				
				$approveNode = $result; //the node in the wf db which is an approve node
				
				/**************
				Query to get the next node (node_reject OR node_approve) from node ID
		
				***************/
				$nodeID = $node;
				//approve field
				$sql = "SELECT rowan_nodes.node_reject
					FROM rowan_nodes
				    WHERE rowan_nodes.id_rowan_nodes =".$nodeID.";"; // the dot does string concat

				$result = $conn->query($sql);

				if ($result->num_rows > 0) {
			    // output data of each row
					echo "NotApprove/Reject node from node id<br><blockquote>";
				    while($row = $result->fetch_assoc()) {
				        echo "node_reject: ".$row["node_reject"]."<br>";
				    }
					echo "</blockquote>";
				} else {
				    echo "0 results";
				}
				//End Query
				
				$rejectNode = $result; //sets the node in wf db that is the reject state
				
				$groups = array();
				//Creates list of users who will approve.
				if (strpos($group1, "Department") !== false){//check if current state is a department
					$group2 = getGroupByName($department); //get group with name of department
					$users = self::approversArray($group1, $group2);//create users array with current group and the department
				}
				elseif(strpos($group1, "College") !== false){//check if current state is college
					$group2 = getGroupByName($college); //get group with name of college
					$users = self::approversArray($group1, $group2);//create users array with current group and the college
				}
				else(){//higher then college or department
					$users = self::approversArray($group1, $group1); //create group with only members of the current group
				}
				
				if($approveNode != null){ //creates the approve state associated with it
					
					/**************
					Query to get the groups/document_state field from node ID
			
					***************/
					$nodeID = $approveNode;
					//groups field
					$sql = "SELECT rowan_nodes.groups
						FROM rowan_nodes
						WHERE rowan_nodes.id_rowan_nodes =".$nodeID.";"; // the dot does string concat
					//document_state 
					/*
					 $sql = "SELECT rowan_nodes.document_state
					FROM rowan_nodes
					 WHERE rowan_nodes.id_rowan_nodes =".$nodeID.";"; // the dot does string concat
					*/
		
					//execute query
					$result = $conn->query($sql);
	
					if ($result->num_rows > 0) {
					// output data of each row
						echo "Groups from node id<br><blockquote>";
						while($row = $result->fetch_assoc()) {
							echo "groups: ".$row["groups"]."<br>";
						}
						echo "</blockquote>";
					} else {
						echo "0 results";
					}
					//End Query
					
					$approveGroupName = $result; //groupname of approval state
					$approveState = $dms->getWorkFlowStateByName($approveGroupName); // get approval state from dms

					$action = $dms->getWorkflowActionByName("Approve"); //get Approve action from dms (discuss if needs to be the "Advance" action)
					
					$wf->addTransition($state1, $action, $approveState, $users, $groups); //create the transition and add it to the wf
				}
				if(rejectNode != null){ //creates the reject state associated with it
					
					/**************
					Query to get the groups/document_state field from node ID
			
					***************/
					$nodeID = $rejectNode;
					//groups field
					$sql = "SELECT rowan_nodes.groups
						FROM rowan_nodes
						WHERE rowan_nodes.id_rowan_nodes =".$nodeID.";"; // the dot does string concat
					//document_state 
					/*
					 $sql = "SELECT rowan_nodes.document_state
					FROM rowan_nodes
					 WHERE rowan_nodes.id_rowan_nodes =".$nodeID.";"; // the dot does string concat
					*/
		
					//execute query
					$result = $conn->query($sql);
	
					if ($result->num_rows > 0) {
					// output data of each row
						echo "Groups from node id<br><blockquote>";
						while($row = $result->fetch_assoc()) {
							echo "groups: ".$row["groups"]."<br>";
						}
						echo "</blockquote>";
					} else {
						echo "0 results";
					}
					//End Query
					
					$rejectGroupName = $result; //reject state name set by group name assosiated to it
					$rejectState = $dms->getWorkFlowStateByName($rejectGroupName); //set the state using the groupname
					
					$action = $dms->getWorkflowActionByName("Not Approved");//get Not Approved action from dms
					
					$wf->addTransition($state1, $action, $rejectState, $users, $groups); //create transition using reject state
				}
			}
			return $wf; //return the wf created
		}
		
		/**
		 *Creates an array of users that will approve at a given stave.
		 *Uses group1 and group2 to make decision.
		 *Think "College Dean" and "College of Science" as groups needed
		 *
		 *@return array of User objects.
		 */
		public static function approversArray($group1, $group2){
			$users1 = $group1->getUsers();
				
				foreach($users1 as $user){
					if($user->isMemberOfGroup($group2)){
						$users[] = $user;
					}
				}
			return $users;
		}
	}
	
$conn->close();
?>	
		
		
		
		
				
		
		

//  If above code isn't working. Try adding some of these at the top - Paul
/*
include("../inc/inc.Settings.php");
include("../inc/inc.LogInit.php");
include("../inc/inc.Utils.php");
include("../inc/inc.Language.php");
include("../inc/inc.Init.php");
include("../inc/inc.Extension.php");
include("../inc/inc.DBInit.php");
include("../inc/inc.ClassUI.php");
include("../inc/inc.Authentication.php");
include("../inc/inc.ClassPasswordStrength.php");*/




