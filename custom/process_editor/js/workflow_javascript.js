/*
 * JS for RowanProcessEditor
 * Author: Justin Gavin (jtg)
 * 		http://justingavin.com/
 */

var openModal = "";
function setListeners(){
	document.getElementById("newProcessButton").onclick = function() {
        showModal("newProcessHTML");
    };
	
	document.getElementById("delProcessButton").onclick = function() {
        showModal("delProcessHTML");
    };
    
    // Get the <span> element that closes the modal
    document.getElementById("pe-gen-close").onclick = function() {
        document.getElementById('pe-gen-modal').style.display = "none";
		if (openModal !== "")
		{
			document.getElementById(openModal).style.display = "none";
		}
    };
	
	
	$("#newProcessForm").submit(function(e) {

		$.ajax({
			   type: "POST",
			   url: "../custom/process_editor/ajax/add_process.php",
			   data: $("#newProcessForm").serialize(), // serializes the form's elements.
			   dataType: 'json', // what type of data do we expect back from the server
                        encode          : true
			   
			 })
		
			.done(function( data ) {
				
				
				if ( ! data.success) {
				
					// handle errors for process_type ---------------
					if (data.errors.process_type) {
						alert(data.errors.process_type);
					}
		
					// handle errors for sql ---------------
					if (data.errors.sql) {
						alert(data.errors.sql);
					}

	
			} else {
				
				location.reload(true);
	
			}

			});
		
		e.preventDefault(); // avoid to execute the actual submit of the form.
	});
	
	
	$("#delProcessForm").submit(function(e) {

		$.ajax({
			   type: "POST",
			   url: "../custom/process_editor/ajax/del_process.php",
			   data: $("#delProcessForm").serialize(), // serializes the form's elements.
			   dataType: 'json', // what type of data do we expect back from the server
                        encode          : true
			   
			 })
		
			.done(function( data ) {
				
				
				if ( ! data.success) {
				
					// handle errors for process_type ---------------
					if (data.errors.rowanWorkflowID) {
						alert(data.errors.rowanWorkflowID);
					}
		
					// handle errors for sql ---------------
					if (data.errors.sql) {
						alert(data.errors.sql);
					}

	
			} else {
				
				location.reload(true);
	
			}

			});
		
		e.preventDefault(); // avoid to execute the actual submit of the form.
	});
	
	
	
}

//contentID is the ID of the div to show
function showModal(contentID)
{
	
	document.getElementById('pe-gen-modal').style.display = "block";
	document.getElementById(contentID).style.display = "block";
	openModal = contentID;
}

// Main =============================================
$( document ).ready(function() {
	setListeners();
});
