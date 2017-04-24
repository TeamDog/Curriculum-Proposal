<?php
/*
 * Script to be called by ProcessEditor
 * Author: Justin Gavin (jtg)
 * 		http://justingavin.com/
 */

include("mysql_rowan_cpms.php");

function del_process () {
	$errors         = array();      // array to hold validation errors
	$data           = array();      // array to pass back data
	
	// validate the variables ======================================================
		// if any of these variables don't exist, add an error to our $errors array
	
		if (empty($_POST['rowanWorkflowID']))
			$errors['rowanWorkflowID'] = 'Invalid ID.';
	
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
					
			$rowanWorkflowID=$_POST["rowanWorkflowID"];		
			
			//Delete associated nodes
			$sqlResult = $rowanConn->query(
			'DELETE FROM rowan_cpms.rowan_nodes WHERE associated_workflow="'.$rowanWorkflowID.'";'
				);
			if(!$sqlResult)
			{
				$errors['sql'] = $rowanConn->error;
				$data['success'] = false;
				$data['errors']  = $errors;
				return $data;
			}
			
			//Delete process
			$sqlResult = $rowanConn->query(
			'DELETE FROM rowan_cpms.rowan_workflows WHERE id_rowan_workflows="'.$rowanWorkflowID.'";'
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
    echo json_encode(del_process());


?>