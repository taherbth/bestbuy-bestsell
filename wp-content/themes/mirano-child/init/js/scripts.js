jQuery(document).ready(function($){
	
	function fadeOutNotification(){
	  $('.message.updated').fadeOut(300);
	}
	setTimeout(fadeOutNotification, 5000);
	
	$('.message.updated .close').click(function(){
		$(this).parent().fadeOut(300);
	});
	
	var window_height = $( window ).height();
	$('.full-height-bg').height(window_height);
	$('.full-height-bg .et_pb_container').height(window_height);
    
	
	$('.registration-form .account_private').click(function(){
		$('.registration-form .private-tab').show();
		$('.registration-form .business-tab').hide();
	});
	$('.registration-form .account_business').click(function(){
		$('.registration-form .private-tab').hide();
		$('.registration-form .business-tab').show();
	});
	
	$(".my-profile .profile-menu .profile-menu-item.my-profile-tab-profile").click(function(e){
		e.preventDefault();
		$(this).addClass('active');
		$('.my-profile form.my-profile-tab-profile').show();
		$('.my-profile form.my-profile-tab-business').hide();
		$('.my-profile form.my-profile-tab-password').hide();
		$('.my-profile .profile-menu-item.my-profile-tab-business').removeClass('active');
		$('.my-profile .profile-menu-item.my-profile-tab-password').removeClass('active');
	});
	
/*
	$(".my-profile .profile-menu .profile-menu-item.my-profile-tab-business").click(function(e){
		e.preventDefault();
		$(this).addClass('active');
	   $('.my-profile form.my-profile-tab-business').show();
	   $('.my-profile form.my-profile-tab-profile').hide();
	   $('.my-profile form.my-profile-tab-password').hide();
	   $('.my-profile .profile-menu-item.my-profile-tab-profile').removeClass('active');
	   $('.my-profile .profile-menu-item.my-profile-tab-password').removeClass('active');
	});
*/
	
	$(".my-profile .profile-menu .profile-menu-item.my-profile-tab-password").click(function(e){
		e.preventDefault();
		$(this).addClass('active');
	   $('.my-profile form.my-profile-tab-password').show();
	   $('.my-profile form.my-profile-tab-profile').hide();
	   $('.my-profile form.my-profile-tab-business').hide();
	   $('.my-profile .profile-menu-item.my-profile-tab-profile').removeClass('active');
	   $('.my-profile .profile-menu-item.my-profile-tab-business').removeClass('active');
	});
	
	/*
		Register AJAX
	*/
	
	function add_message($this, $message){
		if(!$this.parent().find('.validation').length){
			$this.before('<div class="validation">'+ $message +'</div>');   
	    }else{
		    $this.parent().find('.validation').html($message);
	    }
	}
	
	$('.registration-form :input').change(function(e) {
		$(this).each(function() {
		    switch($(this).attr('name')) {
			    case 'password':
			        if($(this).val().length < 8){ add_message($(this), 'Lösenordet måste vara minst 8 tecken långt!'); } else { add_message($(this), '<i class="fa inmid-check fa-check"></i>'); }
			        break;
			    case 'email':
			    	var re = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i;
					if(re.test($(this).val()) == false) { add_message($(this), 'Ange en riktig e-postadress.'); } else { add_message($(this), '<i class="fa inmid-check fa-check"></i>'); }
			    	break;
			    case 'email_repeat':
			    	var re = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i;
					if(re.test($(this).val()) == false) {
						add_message($(this), 'Ange en riktig e-postadress.');
					} else {
						if($(this).closest('.registration-form').find('input[name="email"]').val() == $(this).val()) {
							add_message($(this), '<i class="fa inmid-check fa-check"></i>');
						} else {
							add_message($(this), 'E-post adresserna matchar inte varandra.');
						}
					}
			    	break;
			    case 'first_name':
			    	if($(this).val().length < 2){ add_message($(this), 'Ditt förnamn är inte giltigt!'); } else { add_message($(this), '<i class="fa inmid-check fa-check"></i>'); }
			    	break;
			    case 'last_name':
			    	if($(this).val().length < 2){ add_message($(this), 'Ditt efternamn är inte giltigt!'); } else { add_message($(this), '<i class="fa inmid-check fa-check"></i>'); }
			    	break;
			    case 'phone':
			    	var re = /.+\d+$/;
			    	var ree = /^[0-9,.]*$/;
			    	var phone = $(this).val();
			    	phone = phone.replace('+', '');
			    	
			    	if(re.test($(this).val()) == false && ree.test(phone) == false) { add_message($(this), 'Ange ett riktigt telefonummer.'); } else { if($(this).val() == ''){ add_message($(this), 'Fältet är tomt.'); } else { add_message($(this), '<i class="fa inmid-check fa-check"></i>'); } }
			    	break;
			    case 'business_name':
			    	if($(this).val().length < 4){ add_message($(this), 'Företagsnamnet är inte giltigt!'); } else { add_message($(this), '<i class="fa inmid-check fa-check"></i>'); }
			    	break;
			    case 'business_vat_no':
			    	if($(this).val().length < 10){ add_message($(this), 'Organisationsnummet är inte giltigt!'); } else { add_message($(this), '<i class="fa inmid-check fa-check"></i>'); }
			    	break;
			    case 'business_adress':
			    	if($(this).val().length < 5){ add_message($(this), 'Ange en giltig adress.'); } else { add_message($(this), '<i class="fa inmid-check fa-check"></i>'); }
			    	break;
			    case 'business_postal_code':
			    	if($(this).val().length < 3){ add_message($(this), 'Ange ett giltigt postnummer.'); } else { add_message($(this), '<i class="fa inmid-check fa-check"></i>'); }
			    	break;
			    case 'business_city':
			    	if($(this).val().length < 3){ add_message($(this), 'Ange en giltig stad.!'); } else { add_message($(this), '<i class="fa inmid-check fa-check"></i>'); }
			    	break;
			}
	    });
		
	});
	
	$('.registration-form').submit(function(e) {
        e.preventDefault();
        
        $this = $(this);
		
		var $inputs = $('.registration-form :input');
		$account_type = $(this).find('.account_type:checked').val();
	    var values = {};
	    $inputs.each(function() {
	        values[this.name] = $(this).val();
	    });
	    $valid = true;
	    if($account_type == 'account_private'){
		    
		    $inputs.each(function() {
		        switch($(this).attr('name')) {
			        case 'username':
			        	var illegalChars = /\W/;
			        	if(illegalChars.test($(this).val())){ add_message($(this), 'Användarnamnet innehåller ogiltig tecken!'); $valid = false;  } else { if($(this).val() == ''){ add_message($(this), 'Fältet är tomt.'); } else { add_message($(this), '<i class="fa inmid-check fa-check"></i>'); } }
				        break;
				    case 'password':
				        if($(this).val().length < 8){ add_message($(this), 'Lösenordet måste vara minst 8 tecken långt!'); $valid = false;  } else { add_message($(this), '<i class="fa inmid-check fa-check"></i>'); }
				        break;
				    case 'email':
				    	var re = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i;
						if(re.test($(this).val()) == false) { add_message($(this), 'Ange en riktig e-postadress.'); $valid = false; } else { add_message($(this), '<i class="fa inmid-check fa-check"></i>'); }
				    	break;
				    case 'email_repeat':
				    	var re = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i;
						if(re.test($(this).val()) == false) {
							add_message($(this), 'Ange en riktig e-postadress.'); $valid = false;
						} else {
							if($(this).closest('.registration-form').find('input[name="email"]').val() == $(this).val()) {
								add_message($(this), '<i class="fa inmid-check fa-check"></i>');
							} else {
								add_message($(this), 'E-post adresserna matchar inte varandra.'); $valid = false;
							}
						}
				    	break;
				    case 'first_name':
				    	if($(this).val().length < 2){ add_message($(this), 'Ditt förnamn är inte giltigt!'); $valid = false; } else { add_message($(this), '<i class="fa inmid-check fa-check"></i>'); }
				    	break;
				    case 'last_name':
				    	if($(this).val().length < 2){ add_message($(this), 'Ditt efternamn är inte giltigt!');  $valid = false;} else { add_message($(this), '<i class="fa inmid-check fa-check"></i>'); }
				    	break;
				    case 'phone':
				    	var re = /.+\d+$/;
				    	var ree = /^[0-9,.]*$/;
				    	var phone = $(this).val();
				    	phone = phone.replace('+', '');
				    	
				    	if(re.test($(this).val()) == false && ree.test(phone) == false) { add_message($(this), 'Ange ett riktigt telefonummer.'); } else { if($(this).val() == ''){ add_message($(this), 'Fältet är tomt.'); } else { add_message($(this), '<i class="fa inmid-check fa-check"></i>'); } }
				    	break;
				}
		    });
	    } else if($account_type == 'account_business'){
		    $valid = true;
		    $inputs.each(function() {
		        switch($(this).attr('name')) {
			        case 'username':
			        	var illegalChars = /\W/;
			        	if(illegalChars.test($(this).val())){ add_message($(this), 'Användarnamnet innehåller ogiltig tecken!'); $valid = false;  } else {if($(this).val() == ''){ add_message($(this), 'Fältet är tomt.'); } else { add_message($(this), '<i class="fa inmid-check fa-check"></i>'); } }
				        break;
				    case 'password':
				        if($(this).val().length < 8){ add_message($(this), 'Lösenordet måste vara minst 8 tecken långt!'); $valid = false;  } else { add_message($(this), '<i class="fa inmid-check fa-check"></i>'); }
				        break;
				    case 'email':
				    	var re = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i;
						if(re.test($(this).val()) == false) { add_message($(this), 'Ange en riktig e-postadress.'); $valid = false; } else { add_message($(this), '<i class="fa inmid-check fa-check"></i>'); }
				    	break;
				    case 'email_repeat':
				    	var re = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i;
						if(re.test($(this).val()) == false) {
							add_message($(this), 'Ange en riktig e-postadress.'); $valid = false;
						} else {
							if($(this).closest('.registration-form').find('input[name="email"]').val() == $(this).val()) {
								add_message($(this), '<i class="fa inmid-check fa-check"></i>');
							} else {
								add_message($(this), 'E-post adresserna matchar inte varandra.'); $valid = false;
							}
						}
				    	break;
				    case 'business_name':
				    	if($(this).val().length < 4){ add_message($(this), 'Företagsnamnet är inte giltigt!'); $valid = false; } else { add_message($(this), '<i class="fa inmid-check fa-check"></i>'); }
				    	break;
				    case 'business_vat_no':
				    	if($(this).val().length < 10){ add_message($(this), 'Organisationsnummet är inte giltigt!'); $valid = false; } else { add_message($(this), '<i class="fa inmid-check fa-check"></i>'); }
				    	break;
				    case 'business_adress':
				    	if($(this).val().length < 5){ add_message($(this), 'Ange en giltig adress.'); $valid = false; } else { add_message($(this), '<i class="fa inmid-check fa-check"></i>'); }
				    	break;
				    case 'business_postal_code':
				    	if($(this).val().length < 3){ add_message($(this), 'Ange ett giltigt postnummer.'); $valid = false; } else { add_message($(this), '<i class="fa inmid-check fa-check"></i>'); }
				    	break;
				    case 'business_city':
				    	if($(this).val().length < 3){ add_message($(this), 'Ange en giltig stad.!'); $valid = false; } else { add_message($(this), '<i class="fa inmid-check fa-check"></i>'); }
				    	break;
				}
		    });
	    }
	    if($valid == true){
	        $.ajax({
	            type: 'POST',
	            url: form_register_obj.ajaxurl,
	            data: { 
	                'action': 'ajax_form_registration',
	                'account_type': $account_type,
	                'form_values': values,
	            },
	            success: function(data){
		            alert(data);
		            window.location = form_register_obj.redirect_to;
	            }
	        });
        }
    });
    
    
    $('.registration-form :input[name="username"]').on('change', function(e) {
	    $username = $(this).val();
	    $this = $(this);
	    
	    if(!$this.parent().find('.validation').length){
			$(this).before('<div class="validation"><i class="fa username-spinner fa-refresh fa-spin"></i></div>');   
	    }else{
		    $(this).prev('.validation').html('<i class="fa username-spinner fa-refresh fa-spin"></i>');
	    }
	    
        $.ajax({
            type: 'POST',
            url: form_register_obj.ajaxurl,
            data: { 
                'action': 'does_username_exists',
                'username': $username
            },
            success: function(data){
	            if(data == 1){
		            $this.prev('.validation').html('<i class="fa inmid-warning fa-exclamation-circle"></i> Användarnamn finns redan!');
	            } else {
		            if($username.length < 5){
			            $this.prev('.validation').html('<i class="fa inmid-warning fa-exclamation-circle"></i> Användarnamnet måste vara minst 5 tecken långt!');
		            } else {
		            	$this.prev('.validation').html('<i class="fa inmid-check fa-check"></i>');
		            }
	            }
	            
            }
        });
    });
	
	
	
	$(document).on('click touchstart', ".menu-icon", function(e) {
		e.preventDefault();
        $('#page-container, .mobile-side-menu').toggleClass('mobile-menu-opened');
    });
    
    $(document).on('click touchstart', '#page-container.mobile-menu-opened, \
    #page-container.mobile-menu-opened', function(e) {
		e.preventDefault();
		$('#page-container, .mobile-side-menu').removeClass('mobile-menu-opened');
    });
    
    $(document).on('click touchstart', '#page-container.mobile-menu-opened, \
    #page-container.mobile-menu-opened', function(e) {
		e.preventDefault();
		$('#page-container, .mobile-side-menu').removeClass('mobile-menu-opened');
    });
        
    $(document).on('click touchstart', '.open-child', function(e) {
	   	e.preventDefault();
	   	
	   	$(this).parent().find("> .sub-menu").slideToggle();
    });
    
    $( ".mobile-side-menu ul .menu-item-has-children" ).each(function() {
		$(this).append('<a href="#" class="open-child"></a>')
	});
	
	$('.alert .close').live('click', function() {
		
		$(this).parent().fadeOut(600);
		
	});
	
	$('.searchandfilter li[data-sf-field-input-type="checkbox"] ul li label input:checkbox').change(function(){
        if($(this).is(":checked")) {
            $(this).parent().addClass("checked");
            
/*
            $.each($(this).closest('ul[data-operator]').find('> li'), function(){
	            $(this).addClass('hide');
            });
*/
            $(this).closest('li').addClass("checked");
        } else {
            $(this).parent().removeClass("checked");
/*
            $.each($(this).closest('ul[data-operator]').find('> li'), function(){
	            $(this).removeClass('hide');
            });
*/
            $(this).closest('li').removeClass("checked");
        }
    });
	
	$('.toggle-button').on('click', function(e) {
		e.preventDefault();
		$(this).next('.toggle-content').toggleClass('toggle-opened');
	});
	
	var et_pb_carousel = $(".owl-portfolio");
 
/*
	  et_pb_carousel.owlCarousel({
		  navigation : true,
		  navigationText : [" "," "],
		  'items': 5,
		  itemsDesktop : [1600,4],
		  itemsDesktopSmall : [1024,3],
		  itemsTablet: [768,2],
		  itemsMobile : [480,1],
	  });
*/
	  
	  	if($( window ).width() > 1199){
		  	var columns = 4;
	  	} else if($( window ).width() > 768) {
		  	var columns = 3;
	  	} else if($( window ).width() > 480) {
		  	var columns = 2;
	  	} else {
		  	var columns = 2;
	  	}
	  	
	  	
	  	var portfolio_item_width = $('.owl-carousel .owl-item').width();
		var portfolio_item_height = portfolio_item_width * .75;
		
		$('.owl-portfolio .owl-item').css({ 'height' : portfolio_item_height });
		
		$( window ).resize(function() {
			
			if($( window ).width() > 1199){
			  	var columns = 4;
		  	} else if($( window ).width() > 980) {
			  	var columns = 3;
		  	} else if($( window ).width() > 768) {
			  	var columns = 2;
		  	} else if($( window ).width() > 479) {
			  	var columns = 1;
		  	} else { 
			  	var columns = 1;
		  	}
		  	
		  	
		  	var portfolio_item_width = $('.owl-carousel .owl-item').width();
			var portfolio_item_height = portfolio_item_width * .75;
			
			
			$('.owl-portfolio .owl-item').css({ 'height' : portfolio_item_height });
				
		});
		
	 
	  // Custom Navigation Events
	  $(".owl-portfolio .et-pb-slider-arrows .et-pb-arrow-next").click(function(){
	    et_pb_carousel.trigger('owl.next');
	  });
	  $(".owl-portfolio .et-pb-slider-arrows .et-pb-arrow-prev").click(function(){
	    et_pb_carousel.trigger('owl.prev');
	  });
    
    //jQuery Tabs
	$('ul.tabs').each(function(){
		
		var active = $($(this).find('a')[0]);
		
		var content = $("#"+active.data('target'));
		active.addClass("active");
		content.addClass("active");
		
		$(this).on('click', 'a', function() {
			if($(this).hasClass('active')) {
				return;
			}
			active.removeClass("active");
			content.removeClass("active");
			// content.slideToggle(300);
						
			active = $(this);
			var target = $(this).data('target');
			
			content = $("#"+target);
			setTimeout(function() {
				active.addClass('active');
				content.addClass("active");	
				// content.slideToggle(300);
			}, 500);
			
			
			e.preventDefault();
		});
		
	});
	
});