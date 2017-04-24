<?php
/*
 * Script to be called by ProcessEditor
 * Author: Justin Gavin (jtg)
 * 		http://justingavin.com/
 */

include("mysql_rowan_cpms.php");

function add_node() {
	$errors         = array();      // array to hold validation errors
	$data           = array();      // array to pass back data
	
	// validate the variables ======================================================
		// if any of these variables don't exist, add an error to our $errors array
	
	
	//This garbage has rowan_ prefixed to the front because of a debugging situation that I am too lazy to fix, not hurting anything having rowan_ in front, ignore it.
		if (empty($_POST['associated_workflow']))
			$errors['associated_workflow'] = 'associated_workflow is required.';
		if (empty($_POST['rowan_document_state']) && $_POST['rowan_document_state'] != 0)
			$errors['rowan_document_state'] = 'rowan_document_state is required.';
			
		if (empty($_POST['rowan_name']))
			$errors['rowan_name'] = 'rowan_name is required.';
			
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
					
			$associated_workflow=$_POST["associated_workflow"];
			$rowan_name=$_POST["rowan_name"];
			$rowan_document_state=$_POST["rowan_document_state"];
			
			
			$sqlResult = $rowanConn->query('INSERT INTO rowan_cpms.rowan_nodes (document_state, associated_workflow, name) VALUES ("'.$rowan_document_state.'", "'.$associated_workflow.'", "'.$rowan_name.'");');
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
    echo json_encode(add_node());


?>
