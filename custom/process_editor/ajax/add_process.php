<?php
/*
 * Script to be called by ProcessEditor
 * Author: Justin Gavin (jtg)
 * 		http://justingavin.com/
 */

include("mysql_rowan_cpms.php");

function add_process() {
	$errors         = array();      // array to hold validation errors
	$data           = array();      // array to pass back data
	
	// validate the variables ======================================================
		// if any of these variables don't exist, add an error to our $errors array
	
		if (empty($_POST['process_type']))
			$errors['process_type'] = 'Process Type is required.';
	
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
					
			$processType=$_POST["process_type"];		
			
			//Create new process
			$sqlResult = $rowanConn->query('INSERT INTO rowan_cpms.rowan_workflows SET process_type="'.$processType.'";');
			if(!$sqlResult)
			{
				$errors['sql'] = $rowanConn->error;
				$data['success'] = false;
				$data['errors']  = $errors;
				return $data;
			}
			
			//Save new process ID
			$sqlResult = $rowanConn->query('SELECT id_rowan_workflows FROM rowan_cpms.rowan_workflows WHERE process_type ="'.$processType.'";');
			$row = $sqlResult->fetch_assoc();
			$processID = $row['id_rowan_workflows'];
			
			//create initial node
			$sqlResult = $rowanConn->query(
				'INSERT INTO rowan_cpms.rowan_nodes
				SET document_state="0",
				associated_workflow='.$processID.';'
				);
			if(!$sqlResult)
			{
				$errors['sql'] = $rowanConn->error;
				$data['success'] = false;
				$data['errors']  = $errors;
				return $data;
			}
			
			//Link new process to init node
			$sqlResult = $rowanConn->query(
			'UPDATE rowan_cpms.rowan_workflows SET initial_node=(SELECT id_rowan_nodes FROM rowan_cpms.rowan_nodes WHERE associated_workflow="'.$processID.'") WHERE id_rowan_workflows="'.$processID.'";'
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
    echo json_encode(add_process());


?>