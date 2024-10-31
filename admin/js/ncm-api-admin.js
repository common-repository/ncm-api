jQuery(function(){
	if(jQuery('#ncm-api-table').length > 0){
		jQuery('#ncm-api-table').DataTable();
	}

	if(jQuery('#ncm-order-table').length > 0){
		jQuery('#ncm-order-table').DataTable();
	}

	
});

function refreshPage(){
    window.location.reload();
}
