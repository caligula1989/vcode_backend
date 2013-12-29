function deleteReport(id){
  $.get( "?controller=Products&action=deleteReport&id="+id, function( data ) {
    removeFromList(id);
  });
}

function approveReport(id){
  $.get( "?controller=Products&action=approveReport&id="+id, function( data ) {
    removeFromList(id);
  });
    
}

function removeFromList(id){
 $("#product_row_"+id).remove(); 
}