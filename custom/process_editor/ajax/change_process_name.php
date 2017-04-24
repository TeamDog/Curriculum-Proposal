<?php
/*
 * Script to be called by ProcessEditor
 * Author: Justin Gavin (jtg)
 * 		http://justingavin.com/
 */

include("mysql_rowan_cpms.php");

function change_process_name()
{
	$errors         = array();      // array to hold validation errors
	$data           = array();      // array to pass back data
	
	// validate the variables ======================================================
		// if any of these variables don't exist, add an error to our $errors array
	
		if (empty($_POST['process_id']))
			$errors['process_id'] = 'Invalid ID.';
	
		if (empty($_POST['new_type']))
			$errors['new_type'] = 'Invalid Type.';
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
					
			$processID=$_POST['process_id'];
			$newType =$_POST['new_type'];
			//DB connection
			$rowanConn = mysql_rowan_cpms();
			
			$sqlResult = $rowanConn->query(
			'UPDATE rowan_cpms.rowan_workflows SET process_type="'.$newType.'" WHERE id_rowan_workflows="'.$processID.'";'
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
    echo json_encode(change_process_name());


?>