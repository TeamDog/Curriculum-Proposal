<?php
//<p class='demo'><a href='../custom/class.RowanCoverLetterGenerator.php' target='_blank' class='demo'>[Cover Letter]</a></p>
//require_once("class.Bootstrap.php");
require('../custom/fpdf/class.FPDF.php');

class RowanCoverLetterGenerator /* extends SeedDMS_Bootstrap_Style*/ {
	

	/**
	 * Should generate a cover letter when done.
	 *
	 * @param Object A document object.
	 */
	function generateCoverLetter() {

		
		$document = $_SESSION['varDoc'];

		
		$pdfMode = False; //Set to True for function to create a pdf, False to just echo the informtion


		if($pdfMode) {
			//Creating the PDF
			$pdf = new FPDF('P', 'in', 'letter');
			//$pdf->SetMargin(1, 1, 1);
			$pdf->AddPage();
		}

		
		$author = $document->getOwner(); //This is an author object
		$title = $document->getName();
		$docId = $document->getId();
		$date = date("m/d/y", $document->getDate());
		
		//Want to use this to determin years.
		$month = date("m", $document->getDate());
		$year = date("y", $document->getDate());
		
		
		/*****
		1. Create a database connection.
		*****/
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
		/***** 1. End *****/
		
		
		/*****
		2. Create and fill Atrribute arrays with results of the 
		*****/
		
		$attArray = array(); //Array that will hold the Attribute types
		$valueArray = array(); //Array that will hold the Attribute values
		
		//Get all the attribute IDs and values associated with the document
		$sqlValue = "SELECT seeddms.tblDocumentAttributes.attrdef, seeddms.tblDocumentAttributes.value
					FROM seeddms.tblDocumentAttributes
					WHERE seeddms.tblDocumentAttributes.document= \"".$docId."\";"; 
		//execute query
		$resultValue = mysqli_query($conn, $sqlValue);
		//check if there is anything in it
		if(!$resultValue) {
			die("Database value query failed.");
		}
		
		while($row = mysqli_fetch_assoc($resultValue)) {
			$attrID = $row["attrdef"];
			//Query to get Attribute name from an ID
			$sqlAT = "SELECT seeddms.tblAttributeDefinitions.name
				FROM seeddms.tblAttributeDefinitions
				WHERE seeddms.tblAttributeDefinitions.id= \"".$attrID."\";";
			//execute query
			$resultAType = mysqli_query($conn, $sqlAT);
			$attName = mysqli_fetch_assoc($resultAType);
			array_push($attArray, $attName["name"]);
			array_push($valueArray, $row["value"]);
		}
		/***** 2. End *****/
		
		
		/*****
		3. Loop through the arrays to fine the Process Type
		*****/
		$intHolder = 0; //will hold the integer value for the Process Type so we don't echo it out again.
		for($x = 0; $x < count($attArray); $x++) {
			if($attArray[$x] == "Process Type") {
				
				if($pdfMode) {
					$pdf->SetFont('Arial', 'B', 16);
					$pdf->Cell(0, 0, '$attArray[$x]:', 1);
					$pdf->Cell(0, 0, $valueArray[$x], 1, 1, 'C');
					$pdf->Ln();
				} else {
					echo "<h4> <u>" . $attArray[$x] . " " . $valueArray[$x] .  "</u></h4>";
					echo "<hr />";
				}
			} 
		}
		
		
		/***** 3. End *****/
		
		/*****
		4. General Document information
		*****/
		if($pdfMode) {
			$pdf->SetFont('Arial', 'B', 13);
			$pdf->Cell(0, 0, 'Proposal Title:');
			$pdf->SetFont('Arial', '', 13);
			$pdf->Cell(0, 0, $title, 0, 0,'C');
			$pdf->Ln();
	
			$pdf->SetFont('Arial', 'B', 13);
			$pdf->Cell(0, 0, 'Date Submitted:');
			$pdf->SetFont('Arial', '', 13);
			$pdf->Cell(0, 0, $date, 0, 0,'C');
			$pdf->Ln();
	
			$pdf->SetFont('Arial', 'B', 13);
			$pdf->Cell(0, 0, 'Lead Sponsor:');
			$pdf->SetFont('Arial', '', 13);
			$pdf->Cell(0, 0, '$author->getFullName()      Email: ', 0, 0,'C');
			$pdf->Cell(0, 0, $author->getEmail(), 0, 0,'R');
			$pdf->Ln();
		} else {
			echo "<strong>Proposal Title: </strong>" . $title . "<br>";
			echo "<hr />";
			echo "<strong>Date Submitted: </strong>" . $date . "<br>";
			echo "<hr />";
			echo "<strong>Lead Sponsor: </strong>" . $author->getFullName() . "<strong>     Email: </strong>" . $author->getEmail() . "<br>";
			echo "<hr />";
		}
		/***** 4. End *****/
		
        
		/*****
		5. Loop through the rest of the Attribute
		*****/
		for($x = 0; $x < count($attArray); $x++) {
			if($x != $intHolder) {
 				if($pdfMode) {
					$pdf->SetFont('Arial', 'B', 13);
					$pdf->Cell(0, 0, '$attArray[$x]:');
					$pdf->SetFont('Arial', '', 13);
					$pdf->Cell(0, 0, $valueArray[$x], 0, 0,'C');
					$pdf->Ln();				
				} else { 
					echo "<strong> $attArray[$x]: </strong>", $valueArray[$x];
					echo "<hr />";
				}
			}
		}
		/***** 5. End ******/
		
		/*****
		6. Get Workflow approvals so far NOTE: Not sure if I need to get digital signatures
		*****/
		if($pdfMode) {
			$pdf->SetFont('Arial', 'B', 16);
			$pdf->Cell(0, 0, 'APPROVALS', 'B');
			$pdf->Ln();		
		} else {	
			echo "<h4> <u>APPROVALS</u> </h4>";
			echo "<hr/>";
		}
		//Query to get the user ID of approvers and the date they approved the proposal
		$sqlApprovers = "SELECT seeddms.tblWorkflowLog.userid, seeddms.tblWorkflowLog.date, seeddms.tblWorkflowLog.transition
			FROM seeddms.tblWorkflowLog
			WHERE seeddms.tblWorkflowLog.document= \"".$docId."\";";
		//Execute query
		$resultApprovers = mysqli_query($conn, $sqlApprovers);

		$apprNameArray = array();
		$apprDateArray = array();
		$apprTranIDArray = array(); //Need the State's name but we have to start with the Transition ID.
									
		
		while($row = mysqli_fetch_assoc($resultApprovers)) {
			$userID = $row["userid"];
			//Query to get Aprover's name from an ID
			$sqlUserName = "SELECT seeddms.tblUsers.fullName
					FROM seeddms.tblUsers
					WHERE seeddms.tblUsers.id = \"".$userID."\";";
			//execute query
			$resultName = mysqli_query($conn, $sqlUserName);
			$apprName = mysqli_fetch_assoc($resultName);
			array_push($apprNameArray, $apprName["fullName"]);
			array_push($apprDateArray, $row["date"]);
			array_push($apprTranIDArray, $row["transition"]);

		}
		
		//We use that Transition ID we got to get a State ID.
		$apprStateIDArray = array();
		$apprActionIDArray = array();
		for($x = 0; $x < count($apprTranIDArray); $x++) {
			$sqlStateID = "SELECT seeddms.tblWorkflowTransitions.state, seeddms.tblWorkflowTransitions.action 
							FROM seeddms.tblWorkflowTransitions
							WHERE seeddms.tblWorkflowTransitions.id = \"".$apprTranIDArray[$x]."\";";
			$resultStateID = mysqli_query($conn, $sqlStateID);
			$row = mysqli_fetch_assoc($resultStateID);
			array_push($apprStateIDArray, $row["state"]);
			array_push($apprActionIDArray, $row["action"]);
			mysqli_free_result($resultStateID);
		}
		
		//We can finally get the State's name using the State's ID.
		$apprStateNameArray = array();
		for($x = 0; $x < count($apprStateIDArray); $x++) {
			$sqlStateName = "SELECT seeddms.tblWorkflowStates.name 
							FROM seeddms.tblWorkflowStates
							WHERE seeddms.tblWorkflowStates.id = \"".$apprStateIDArray[$x]."\";";
			$resultStateName = mysqli_query($conn, $sqlStateName);
			$row = mysqli_fetch_assoc($resultStateName);
			array_push($apprStateNameArray, $row["name"]);
			mysqli_free_result($resultStateName);
		}
		
		//We can finally get the name of the Action using the Action's ID
		$apprActionNameArray = array();
		for($x = 0; $x < count($apprActionIDArray); $x++) {
			$sqlActionName = "SELECT seeddms.tblWorkflowActions.name
							FROM seeddms.tblWorkflowActions
							WHERE seeddms.tblWorkflowActions.id = \"".$apprActionIDArray[$x]."\";";
			$resultActionName = mysqli_query($conn, $sqlActionName);
			$row = mysqli_fetch_assoc($resultActionName);
			array_push($apprActionNameArray, $row["name"]);
			mysqli_free_result($resultActionName);
		}

		
		for($x = 0; $x < count($apprNameArray); $x++) {
			$apDate = new DateTime($apprDateArray[$x]);
			$apprStateName = preg_replace('/\d/', ' ', $apprStateNameArray[$x]);
			if($pdfMode) {
				$pdf->SetFont('Arial', 'B', 13);
				$pdf->Cell(0, 0, $apprStateName); 
				$pdf->SetFont('Arial', '', 13);
				$pdf->Cell(0, 0, $apprNameArray[$x]);
				$pdf->SetFont('Arial', 'B', 13);
				$pdf->Cell(0, 0, 'Date: ', 0, 0,'C');
				$pdf->SetFont('Arial', '', 13);
				$pdf->Cell(0, 0, $apDate->format('d/m/y'));
				$pdf->Ln();
			} else {
				echo "<b> $apprStateName: </b>$apprNameArray[$x] ", "<b>$apprActionNameArray[$x] on: </b>", $apDate->format('d/m/y');
				echo "<hr />";
			}
		}
		
		/*****
		Add pdf formating to mimic the rest of the form?
		*****/
		
		
		//Free memory
		mysqli_free_result($resultValue);
		mysqli_free_result($resultAType);
		mysqli_free_result($resultApprovers);
		
		
		
		$conn->close();
		$seeddmsConn->close();
		
		if($pdfMode) {		
			$pdf->Output();
		}
		
	}
	
}
?>
