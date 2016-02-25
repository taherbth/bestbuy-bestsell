jQuery(document).ready(function($){

	var minDateAllowed = new Date();
	var numberOfDaysToAdd = 14;
	minDateAllowed.setDate(minDateAllowed.getDate() + numberOfDaysToAdd);

	$("#interest_start_date").datepicker({
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
	$("#add_to_cart_interest_div").on('click', function(e) {
			document.getElementById('product_interest_form').style.display = "block";
			document.getElementById('add_to_cart_interest_div').style.display = "none";
			document.getElementById('submit_my_interest_div').style.display = "block";
	});
	$("#asa_price_is_reasonable").on('click', function(e) {
			 if( document.getElementById('asa_price_is_reasonable').checked ){
				  document.getElementById("interest_date_range").disabled = true;
				  document.getElementById("interest_start_date").disabled = true;
			}else{
				document.getElementById("interest_date_range").disabled = false;
				document.getElementById("interest_start_date").disabled = false;
			}
	});
	$('.join-btn').on( 'click', function (event)
	{
		document.getElementById('first_name_error_message').innerHTML='';
		document.getElementById('last_name_error_message').innerHTML='';
		document.getElementById('password_error_message').innerHTML='';
		document.getElementById('invalid_email_error_message').innerHTML='';
		document.getElementById('user_created_error_message').innerHTML='';
		document.getElementById('company_private_person_error_message').innerHTML='';
		document.getElementById('user_created_error').style.display='none';
		var first_name = $('#first_name').val();
		var last_name = $('#last_name').val();
		var user_email = $('#user_email').val();
		var user_pass = $('#user_pass').val();
		var redirect_to = $('#redirect_to').val();
		var company_or_private_person = "";
		var companyOrPrivateValidate = false;

		var firstNameValidate = validateFirstName( first_name );
		var lastNameValidate = validateLastName( last_name );
		var emailValidate = validateEmail( user_email );
		var passwordValidate = validatePassword( user_pass );
		if( validateCompanyOrPrivate( $('input[type=radio]:checked').length ) )
		{
			companyOrPrivateValidate = true;
			company_or_private_person = document.querySelector('input[name="company_or_private_person"]:checked').value;
		}
		if( firstNameValidate && lastNameValidate && emailValidate && passwordValidate && companyOrPrivateValidate )
		{
			var data = {
				action: 'bestbuy_bestsell_sign_up_user',
				first_name: first_name,
				last_name: last_name,
				user_email: user_email,
				user_pass: user_pass,
				company_or_private_person: company_or_private_person
			 };
			 // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
			 $.post(ajaxurl, data, function( response ) {
				 if( response == 1 )
				 {
					 document.getElementById('user_created_success_message').style.display='block';
					 document.getElementById( 'user_created_success_message' ).innerHTML='User Created Successfully'
					 window.location.href= redirect_to;
				 }
				 else if( response==2  )
				 {
					 document.getElementById('user_created_error').style.display='block';
					 document.getElementById( 'user_created_error_message' ).innerHTML='This E-mail Already Exists';
				 }
				 else
				 {
					 document.getElementById( 'user_created_error_message' ).innerHTML= response
				 }
			 });
		}
		else
		{
			document.getElementById('user_created_error').style.display='block';
			return false;
		}
	});
	$('.login-btn').on( 'click', function (event)
	{
		document.getElementById('login_email_error_message').innerHTML='';
		document.getElementById('login_password_error_message').innerHTML='';
		document.getElementById('login_failed_message').innerHTML='';
		document.getElementById('login_error_message').style.display='none';

		var user_email = $('#login_user_email').val();
		var user_pass = $('#login_user_pass').val();
		var redirect_to = $('#login_redirect_to').val();
		var emailValidate = validateLogInEmail( user_email );
		var passwordValidate = validateLogInPassword( user_pass );

		if( emailValidate && passwordValidate )
		{
			var data = {
				action: 'bestbuy_bestsell_sign_in_user',
				user_email: user_email,
				user_pass: user_pass
			};
			// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
			$.post(ajaxurl, data, function( response ) {
				if( response == 1 )
				{
					document.getElementById('login_success_message').style.display='block';
					document.getElementById( 'login_success_message' ).innerHTML='LoggedIn Successfully'
					window.location.href= redirect_to;
				}
				else if( response==2  )
				{
					document.getElementById('login_error_message').style.display='block';
					document.getElementById( 'login_failed_message' ).innerHTML='Bad User Name Or Password'
				}
			});
		}
		else
		{
			document.getElementById('login_error_message').style.display='block';
			return false;
		}
	});
	$('.save_personal_data_btn').on( 'click', function (event)
	{
		document.getElementById('save_personal_data_success_message').innerHTML='';
		document.getElementById( 'save_personal_data_success_message' ).style.display='none';
		document.getElementById('gender_error_message').innerHTML='';
		document.getElementById('first_name_error_message').innerHTML='';
		document.getElementById('last_name_error_message').innerHTML='';
		document.getElementById('mobile_number_error_message').innerHTML='';
		document.getElementById('company_private_person_error_message').innerHTML='';
		var gender = $('#gender').val();
		var first_name = $('#first_name').val();
		var last_name = $('#last_name').val();
		var day = $('#day').val();
		var month = $('#month').val();
		var year = $('#year').val();
		var mobile_number = $('#mobile_number').val();
		var company_or_private_person = "";
		var companyOrPrivateValidate = false;

		var genderValidate = validateGender( gender );
		var firstNameValidate = validateFirstName( first_name );
		var lastNameValidate = validateLastName( last_name );
		var mobileNumberValidate = validateMobileNumber( mobile_number );

		if( validateCompanyOrPrivate( $('input[type=radio]:checked').length ) )
		{
			companyOrPrivateValidate = true;
			company_or_private_person = document.querySelector('input[name="company_or_private_person"]:checked').value;
		}
		if( genderValidate && firstNameValidate && lastNameValidate && mobileNumberValidate && companyOrPrivateValidate )
		{
			var data = {
				action: 'bestbuy_bestsell_save_personal_data',
				gender: gender,
				first_name: first_name,
				last_name: last_name,
				birth_day: day,
				birth_month: month,
				birth_year: year,
				mobile_number: mobile_number,
				company_or_private_person: company_or_private_person
			};
			// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
			$.post(ajaxurl, data, function( response ) {
				if( response == 1 )
				{
					document.getElementById( 'save_personal_data_success_message' ).style.display='block';
					document.getElementById( 'save_personal_data_success_message' ).innerHTML='Personal Data Saved'
				}
			});
		}
		else
		{
			return false;
		}
	});
	$('.save_password_data_btn').on( 'click', function (event)
	{
		document.getElementById('save_password_data_success_message').innerHTML='';
		document.getElementById( 'save_password_data_success_message' ).style.display='none';
		document.getElementById('save_password_data_error_message').innerHTML='';
		document.getElementById( 'save_password_data_error_message' ).style.display='none';
		document.getElementById('old_password_error_message').innerHTML='';
		document.getElementById('new_password_error_message').innerHTML='';
		document.getElementById('confirm_password_error_message').innerHTML='';
		document.getElementById('password_not_matched_error_message').innerHTML='';
		var old_password = $('#old_password').val();
		var new_password = $('#new_password').val();
		var confirm_password = $('#confirm_password').val();

		var changePasswordValidate = validateChangePassword( old_password, new_password, confirm_password);

		if( changePasswordValidate )
		{
			var data = {
				action: 'bestbuy_bestsell_save_password_data',
				old_password: old_password,
				new_password: new_password,
				confirm_password: confirm_password
			};
			// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
			$.post(ajaxurl, data, function( response ) { 
				if( response == 1 )
				{
					document.getElementById( 'save_password_data_success_message' ).style.display='block';
					document.getElementById( 'save_password_data_success_message' ).innerHTML="The password has been updated successfully"
				}
				else if( response == 2 )
				{
					document.getElementById( 'save_password_data_error_message' ).style.display='block';
					document.getElementById( 'save_password_data_error_message' ).innerHTML="Sorry! Failed to update your account details"
				}
				else if( response == 3 )
				{
					document.getElementById( 'save_password_data_error_message' ).style.display='block';
					document.getElementById( 'save_password_data_error_message' ).innerHTML="Old Password doesn't match the existing password"
				}
			});
		}
		else
		{
			return false;
		}
	});
});

function validateGender( gender ){
	if( gender.length <= 0 )
	{
		document.getElementById('gender_error_message').innerHTML="Gender Can't Be Empty";
		return false;
	}
	else
	{
		return true;
	}
}
function validateFirstName( first_name )
{
	if( first_name.length <= 0 )
	{
		document.getElementById('first_name_error_message').innerHTML="First Name Can't Be Empty";
		return false;
	}
	else
	{
		return true;
	}
}
function validateLastName( last_name )
{
	if( last_name.length <= 0 )
	{
		document.getElementById('last_name_error_message').innerHTML="Last Name Can't Be Empty"
		return false;
	}
	else
	{
		return true;
	}
}
function validateEmail(emailAddress)
{
	var filter = /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;
	if (filter.test(emailAddress))
	{
		return true;
	}
	else
	{
		document.getElementById('invalid_email_error_message').innerHTML='Invalid E-mail address';
		return false;
	}
}
function validatePassword( user_pass )
{
	if( user_pass.length <= 0 )
	{
		document.getElementById('password_error_message').innerHTML = "Password Can't Be Empty";
		return false;
	}
	else if( user_pass.length < 10 )
	{
		document.getElementById('password_error_message').innerHTML='Password Must Be 10 Characters Or Long';
		return false;
	}
	else
	{
		return true;
	}
}
function validateChangePassword( old_password, new_password, confirm_password ){
	var return_value = true;
	if( old_password.length <= 0 )
	{
		document.getElementById('old_password_error_message').innerHTML = "This is a required field";
		return_value = false;
	}
	if( new_password.length <= 0 )
	{
		document.getElementById('new_password_error_message').innerHTML = "This is a required field";
		return_value = false;
	}
	else if( new_password.length < 10 )
	{
		document.getElementById('new_password_error_message').innerHTML="Enter at least 10 characters";
		return_value = false;
	}
	if( confirm_password.length <= 0 )
	{
		document.getElementById('confirm_password_error_message').innerHTML = "This is a required field";
		return_value = false;
	}
	else if( confirm_password.length < 10 )
	{
		document.getElementById('confirm_password_error_message').innerHTML="Enter at least 10 characters";
		return_value = false;
	}
	if( old_password.length > 0 && new_password.length >= 10 && confirm_password.length >= 10 && new_password!=confirm_password)
	{
		document.getElementById('password_not_matched_error_message').innerHTML="Be sure that the passwords match";
		return_value = false;
	}
	return return_value;
}
function validateCompanyOrPrivate( length )
{
	if( length == 0)
	{
		document.getElementById('company_private_person_error_message').innerHTML = "Company Or Private Person Can't Be Empty";
		return false;
	}
	else
	{
		return true;
	}
}
function validateMobileNumber( mobile_number ){
	var regExp = /^07[0-9]{8}$/
	if( regExp.test(mobile_number) )
	{
		return true;
	}else
	{
		document.getElementById('mobile_number_error_message').innerHTML="Incorrect Mobile No";
		return false;
	}
}
function validateLogInEmail(emailAddress)
{
	var filter = /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;
	if (filter.test(emailAddress))
	{
		return true;
	}
	else
	{
		document.getElementById('login_email_error_message').innerHTML='Invalid E-mail address';
		return false;
	}
}
function validateLogInPassword( user_pass )
{
	if( user_pass.length <= 0 )
	{
		document.getElementById('login_password_error_message').innerHTML="Password Can't Be Empty";
		return false;
	}
	else
	{
		return true;
	}
}

