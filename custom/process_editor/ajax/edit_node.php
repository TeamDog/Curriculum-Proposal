<?php
/*
 * Script to be called by ProcessEditor
 * Author: Justin Gavin (jtg)
 * 		http://justingavin.com/
 */

include("mysql_rowan_cpms.php");


function edit_node() {
	$errors         = array();      // array to hold validation errors
	$data           = array();      // array to pass back data
	
	// validate the variables ======================================================
		// if any of these variables don't exist, add an error to our $errors array
	
		if (empty($_POST['node_id']))
			$errors['node_id'] = 'node_id is required.';
		
		if (empty($_POST['document_state']) && $_POST['document_state'] != 0)
			$errors['document_state'] = 'document_state is required.';
		
		if (empty($_POST['group']))
			$errors['group'] = 'group is required.';
			
		if (empty($_POST['node_approve']))
			$errors['node_approve'] = 'node_approve is required.';
		
		if (empty($_POST['node_reject']))
			$errors['node_reject'] = 'node_reject is required.';
		
			
	// return a response ===========================================================
	
		// if there are any errors in our errors array, return a success boolean of false
		if ( ! empty($errors)) {
	
			// if there are items in our errors array, return those errors
			$data['success'] = false;
			$data['errors']  = $errors;
			
		} else {
	
			// if there are no errors process our form, then return a message
	
			//DB connection
			$rowanConn = mysql_rowan_cpms();
			
			//Grab post		
			$node_id=$_POST["node_id"];
			if (!empty($_POST["name"]))
				$name=$_POST["name"];
			else
				$name = "NULL";
			$document_state=$_POST["document_state"];
			$group=$_POST["group"];
			$node_approve=$_POST["node_approve"];
			$node_reject=$_POST["node_reject"];
			
			//Format w/ quotes if necessary
				
			$document_state = '"'.$document_state.'"';
			
			if ($name != "NULL")
				$name = '"'.$name.'"';
			
			if ($group != "NULL")
				$group = '"'.$group.'"';
				
			if ($node_approve != "NULL")
				$node_approve = '"'.$node_approve.'"';
			
			if ($node_reject != "NULL")
				$node_reject = '"'.$node_reject.'"';
			
			
			$sqlResult = $rowanConn->query(
			'UPDATE `rowan_cpms`.`rowan_nodes` SET `group`='.$group.', `document_state`='.$document_state.', `node_approve`='.$node_approve.', `node_reject`='.$node_reject.', `name`='.$name.' WHERE `id_rowan_nodes`='.$node_id.';'
			);
			
			
			if(!$sqlResult)
			{
				$errors['sql'] = $rowanConn->error;
				$data['success'] = false;
				$data['errors']  = $errors;
				return $data;
			}
			
			
			$rowanConn->close();
	
			if (empty($errors)) {
				// show a message of success and provide a true success variable
				$data['success'] = true;
				$data['message'] = 'Success!';
			} else {
				$data['success'] = false;
				$data['errors']  = $errors;
			}
		}

		return $data;
}


    // return all our data to an AJAX call
    echo json_encode(edit_node());

?>