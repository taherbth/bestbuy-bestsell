jQuery(document).ready(function($){
	$('.requiredField').on('keyup', function(e) {
			$("form").validate()
	});

	$('.update_interest_unit_price').on('blur', function(e) {
		
		var interest_id = e.target.id;
		var interest_unit_price =  document.getElementById( interest_id ).value;
        var data = {
            action: 'update_interest_unit_price',
            unit_price: interest_unit_price,
            product_interest_id: interest_id
        };
        // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
        $.post(ajaxurl, data, function( response ) { 
			if( response == 1 ){
				var updated_label = "price_update_message"+interest_id
				document.getElementById( updated_label ).innerHTML="Price Updated"
			}            
        }); 

	});

	$('.show_email_to_interester').on('click', function(e) {
			var interest_id = e.target.id;
			var interester_email_span_id = "interester_email_span"+interest_id	
			document.getElementById( interester_email_span_id ).style.display="block";
	});

	$('.send_email_to_interester_class').on('click', function(e) {
		var interest_id = e.target.id;
		var email_message_to_interester_id = "email_message_to_interester"+interest_id	
		var email_message = document.getElementById( email_message_to_interester_id ).value
        var data = {
            action: 'send_email_to_interester',
            product_interest_id: interest_id,
            email_message_text: email_message
        };
        // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
        $.post(ajaxurl, data, function( response ) { 
			var updated_label = "email_sent_message"+interest_id	
			if( response  ){ document.getElementById( updated_label ).innerHTML="E-mail Sent" }
			else{ document.getElementById( updated_label ).innerHTML="Error: E-mail Not Sent" }            
        });   	
	});

	$('.deal_selection').on('click', function(e) { 
		document.getElementById('send_email').style.display = "block";	
	});

});