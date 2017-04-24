/*
 * JS for RowanProcessEditor_node
 * Author: Justin Gavin (jtg)
 * 		http://justingavin.com/
 */


var QueryString = function () {
  var query_string = {};
  var query = window.location.search.substring(1);
  var vars = query.split("&");
  for (var i=0;i<vars.length;i++) {
    var pair = vars[i].split("=");
        // If first entry with this name
    if (typeof query_string[pair[0]] === "undefined") {
      query_string[pair[0]] = decodeURIComponent(pair[1]);
        // If second entry with this name
    } else if (typeof query_string[pair[0]] === "string") {
      var arr = [ query_string[pair[0]],decodeURIComponent(pair[1]) ];
      query_string[pair[0]] = arr;
        // If third or later entry with this name
    } else {
      query_string[pair[0]].push(decodeURIComponent(pair[1]));
    }
  } 
  return query_string;
}();

var openModal = "";


function setListeners(){
  //console.log(QueryString.rowanWorkflowID);
    
  $(".clickable-row").click(function() {
    //Set init doc state
    var nodeID = $(this).attr("nodeID");
    
    var nodeSelDocState = "#nodeEditDocState_".concat(nodeID);
    $(nodeSelDocState).val($(nodeSelDocState).attr("sel"));
      
      

    //Set init approve 
    var nodeSelApprove = "#nodeEditApprove_".concat(nodeID);
    $(nodeSelApprove).val($(nodeSelApprove).attr("sel"));
    
    //Set init reject 
    var nodeSelReject = "#nodeEditReject_".concat(nodeID);
    $(nodeSelReject).val($(nodeSelReject).attr("sel"));
    
    //Set init group 
    nodeSelGroup = "#nodeEditGroup_".concat(nodeID);
    $(nodeSelGroup).val($(nodeSelGroup).attr("sel"));
    
    showModal("nodeEditHTML_".concat(nodeID));
    
    
    //Setup ajax
    $("#nodeEditForm_".concat(nodeID)).submit(function(e) {

      var formData = {
              'node_id'  : nodeID,
              'name'             : $('#nodeEditName_'.concat(nodeID)).val(),
              'document_state' : $(nodeSelDocState).find(":selected").val().toString(),
              'group' : $(nodeSelGroup).find(":selected").val().toString(),
              'node_approve' : $(nodeSelApprove).find(":selected").val().toString(),
              'node_reject' : $(nodeSelReject).find(":selected").val().toString()
              
          };
    
    
      $.ajax({
           type: "POST",
           url: "../custom/process_editor/ajax/edit_node.php",
           data: formData, 
           dataType: 'json', // what type of data do we expect back from the server
                          encode          : true
           
         })
      
        .done(function( data ) {
          
          
          if ( ! data.success) {
          
            
            if (data.errors.node_id) {
              alert(data.errors.node_id);
            }
            
            if (data.errors.name) {
              alert(data.errors.name);
            }
            
            if (data.errors.document_state) {
              alert(data.errors.document_state);
            }
            
            if (data.errors.group) {
              alert(data.errors.group);
            }
            
            if (data.errors.node_approve) {
              alert(data.errors.node_approve);
            }
            
            if (data.errors.node_reject) {
              alert(data.errors.node_reject);
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
    
     
  });
  
  // Get the <span> element that closes the modal
  document.getElementById("pe-gen-close").onclick = function() {
    document.getElementById('pe-gen-modal').style.display = "none";
    if (openModal !== "")
    {
      document.getElementById(openModal).style.display = "none";
    }
  };
    
    
  document.getElementById("changeTypeButton").onclick = function() {
    showModal("changeTypeHTML");
  };

  
	document.getElementById("newNodeButton").onclick = function() {
    showModal("newNodeHTML");
  };
  
  document.getElementById("delNodeButton").onclick = function() {
    showModal("delNodeHTML");
  };
  
  $("#changeTypeForm").submit(function(e) {
    
    var formData = {
            'process_id'              : QueryString.rowanWorkflowID,
            'new_type'             : $('#changeTypeHTML_new_type').val()
        };
  
    
		$.ajax({
			   type: "POST",
			   url: "../custom/process_editor/ajax/change_process_name.php",
			   data: formData,
			   dataType: 'json', // what type of data do we expect back from the server
                        encode          : true
			   
			 })
		
			.done(function( data ) {
				
				
				if ( ! data.success) {
				
					// handle errors for process_type ---------------
					if (data.errors.new_type) {
						alert(data.errors.new_type);
					}
          
          if (data.errors.process_id) {
						alert(data.errors.process_id);
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
  
	
	$("#newNodeForm").submit(function(e) {

    //This garbage has rowan_ prefixed to the front because of a debugging situation that I am too lazy to fix, not hurting anything having rowan_ in front, ignore it.
    var formData = {
            'associated_workflow'  : QueryString.rowanWorkflowID,
            'rowan_document_state' : $('#newNodeHTML_document_state').find(":selected").val().toString(),
            'rowan_name'             : $('#newNodeHTML_name').val()
        };
  
  
		$.ajax({
			   type: "POST",
			   url: "../custom/process_editor/ajax/add_node.php",
			   data: formData, 
			   dataType: 'json', // what type of data do we expect back from the server
                        encode          : true
			   
			 })
		
			.done(function( data ) {
				
				
				if ( ! data.success) {
				
					
					if (data.errors.associated_workflow) {
						alert(data.errors.associated_workflow);
					}
          
          if (data.errors.rowan_document_state) {
						alert(data.errors.rowan_document_state);
					}
          
          if (data.errors.rowan_name) {
						alert(data.errors.rowan_name);
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
	
  $("#delNodeForm").submit(function(e) {

  
  
    var formData = {
            'rowanNodeID' : $('#delNodeHTML_node_id').find(":selected").val().toString()
        };
  
		$.ajax({
			   type: "POST",
			   url: "../custom/process_editor/ajax/del_node.php",
			   data: formData, // serializes the form's elements.
			   dataType: 'json', // what type of data do we expect back from the server
                        encode          : true
			   
			 })
		
			.done(function( data ) {

				
				if ( ! data.success) {
				
					// handle errors for process_type ---------------
					if (data.errors.rowanNodeID) {
						alert(data.errors.rowanNodeID);
					}
		
					// handle errors for sql ---------------
					if (data.errors.sql) {
						alert(data.errors.sql);
					}

	
			} else {
				location.reload(true);
	
			}

			}).fail(function(jqXHR, textStatus){
        
        location.reload(true);
      });
		
		e.preventDefault(); // avoid to execute the actual submit of the form.
	});
  
	
} // END setListeners()

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


