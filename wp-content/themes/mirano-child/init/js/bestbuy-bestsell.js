jQuery(document).ready(function($){

var minDateAllowed = new Date();
var numberOfDaysToAdd = 14;
minDateAllowed.setDate(minDateAllowed.getDate() + numberOfDaysToAdd); 

    $('#interest_start_date').datepicker({
        dateFormat: 'yy-mm-dd',
        showButtonPanel: true,
        changeMonth: true,
        changeYear: true,
        minDate: minDateAllowed,
        maxDate: '+5Y',
        inline: true
    });

$( "#dialog-link, #icons li" ).hover(
	function() {
		$( this ).addClass( "ui-state-hover" );
	},
	function() {
		$( this ).removeClass( "ui-state-hover" );
	}
);
$('#add_to_cart_interest_div').on('click', function(e) {
		document.getElementById('product_interest_form').style.display = "block";
		document.getElementById('add_to_cart_interest_div').style.display = "none";		
		document.getElementById('submit_my_interest_div').style.display = "block";	
});
	$('#asa_price_is_reasonable').on('click', function(e) {
			 if( document.getElementById('asa_price_is_reasonable').checked ){
				  document.getElementById("interest_date_range").disabled = true;
				  document.getElementById("interest_start_date").disabled = true;
			}else{ 
				document.getElementById("interest_date_range").disabled = false;
				document.getElementById("interest_start_date").disabled = false;
			}			
	});   
});