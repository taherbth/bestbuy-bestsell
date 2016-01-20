jQuery(document).ready(function($){

    $('select#et_pb_showcase_tax').live('change',function(e) {
	    $('body #get_cats_loading_message').show().text('Loading...');
	    $('body .all-tax-categories').hide();
        e.preventDefault();
        var taxanomy_selected = $(this).val();
        if(!current_cats){ var current_cats = 'none' }
        // var taxanomy_selected = this_selector + 'option:selected';
        $.ajax({
            type: 'POST',
            dataType: 'html',
            url: get_tax_cats_obj.ajaxurl,
            data: { 
                'action': 'ajax_get_tax_cats',
                'taxanomy': taxanomy_selected,
                'current_cats': current_cats
            },
            success: function(data){
                $('body .all-tax-categories').html(data);
                $('body .all-tax-categories').show();
                $('body #get_cats_loading_message').hide();
            }
        });
        
    });
    
    $(document).on('click touchstart', ".add_to_cart_interest_div", function(e) {
		e.preventDefault();
        save_interest_ajax();
    });
	function save_interest_ajax(){ 	
		$(document).ready(function($) {
		//var unit_price_id = "interest_unit_price"+interest_id
		//var interest_unit_price =  document.getElementById( unit_price_id ).value 
        var data = {
            action: 'save_my_interest',
            unit_price: '10',
            product_interest_id: '120'
        };
        // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
        $.post(get_tax_cats_obj.ajaxurl, data, function( response ) {
			if( response ){
				var updated_label = "price_update_message"
				
				$('#price_update_message').html('Price Updated');
			}            
        });   
});
}
	function show_product_interest_form(){
		document.getElementById('product_interest_form').style.display = "block";
		document.getElementById('add_to_cart_interest_div').style.display = "none";		
		document.getElementById('submit_my_interest_div').style.display = "block";		
	}
    
    
    
    $('.et-pb-settings').live('click',function(e) {
        
        if( $('.et_pb_modal_settings_container').length ) {
	        $('body #get_cats_loading_message').show().text('Loading...');
	        e.preventDefault();
			var taxanomy_selected = $('body #et_pb_showcase_tax_selected').val();
			var current_cats = $('body #et_pb_showcase_tax_selected_categories').val();
			
	        $.ajax({
            type: 'POST',
            dataType: 'html',
            url: get_tax_cats_obj.ajaxurl,
            data: { 
                'action': 'ajax_get_tax_cats',
                'taxanomy': taxanomy_selected,
                'current_cats': current_cats
            },
            success: function(data){
                $('body .all-tax-categories').html(data);
                $('body #get_cats_loading_message').hide();
            }
        });
	        
        }
        
    });
	
});