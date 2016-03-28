<?php 
	global $product_form,$product_data, $interest_form, $wp_interest_form_data, $product_interest_validation_errors; 
	$current_user_id = get_current_user_id();			
	$user_info = get_userdata($current_user_id);
	$all_meta_for_user = get_user_meta( $current_user_id );
	if($user_info->roles){
		$current_user_role = implode(', ', $user_info->roles) ;
	}
	$today_date = date('Y-m-d');
	$interest_start_date_deafult = date('Y-m-d', strtotime($today_date. ' + 14 days'));
	
	?> 
	<form method="post" enctype="multipart/form-data">
			<!-- Start: product_form div  -->
			<div class="product_form" id="product_form" style="display:block; float:left; ">		
				<div class="interest_error_message">
					<?php 					
						if ( isset( $product_interest_validation_errors ) && sizeof( $product_interest_validation_errors->get_error_messages() )  > 0 )  { 
							echo '<div class="error"><p>';
							foreach ( $product_interest_validation_errors->get_error_messages($code) as $error ) {
								echo $error . "<br />\n";   
							}
							echo '</p></div>';  
						}
					?>
				</div>
			<?php
				if( $product_form ){
					foreach( $product_form as $field ) {
						switch ( $field['type'] ) {
							case 'label': 
								echo $field['label'].'<br />'; 
							break;
							case 'checkbox': 
								echo '<br /><input '. $field['checked'].' type="checkbox" name="'. $field['name']. '" id="'. $field['id']. '"  />&nbsp;'.$field['label']."<br />";	
								break;
								case 'text': // The html to display for the text type
								echo $field['label'];
								echo '<input '. $field['disabled']. ' type="text" name="'. $field['name']. '" id="'. $field['id']. '" value="'.$field['value']. '" ' . $field['attribute'] .'/>'."<br />";
							break;
							case 'file': // The html to display for the text type
								echo $field['label'];
								echo '<input type="file" name="'. $field['name']. '" id="'. $field['id']. '" value="'.$field['value']. '" ' . $field['attribute'] .'/>'."<br />";
							break;
							case 'select': // The html to display for the text type
								echo $field['label'];
								echo '<select '. $field['disabled']. ' name="'. $field['name']. '" id="'. $field['id']. '" >';
								foreach( $field['options'] as $each_option ){		
									if( isset( $product_data[ $field['name'] ] ) && $_POST[ $field['name'] ]===$each_option['value'] ){
										$each_option['selected'] = 'selected';
									}
									echo '<option '. $each_option['selected'].'  value="'.$each_option['value']. '" >' . $each_option['label']. '</option>'; 
								}
								echo '</select >';
							break;
							case 'textarea': // The html to display for the textarea type
								echo $field['label'];
								echo '<textarea name="'. $field['name']. '" id="'.$field['id']. '"placeholder="'. $field['placeholder']. '">'. $field['value']. '</textarea><br />';       
							break;
							case 'texteditor':
							echo $field['label'];
								wp_editor( $field['value'] , 'product-textarea', $field['settings']);       
							break;
						}
					}
				}
			?>
				<div style="margin-bottom:50px;"> 				
				</div>
			</div> <!-- End: product_form div -->	
			<!-- Start: product interest form div -->	
			<div style="margin-left:20%; float:left; "> 
			<?php
				/*********  Show interest form for a single product **********/
				do_action('inmid_product_interest_form');
				if( $interest_form ){
					foreach( $interest_form as $field ) {
						switch ( $field['type'] ) {
							case 'label': 
								echo $field['label'].'<br />'; 
							break;
							case 'checkbox': 
								echo '<br /><input '. $field['checked'].' type="checkbox" name="'. $field['name']. '" id="'. $field['id']. '"  />&nbsp;'.$field['label']."<br />";	
								break;
								case 'text': // The html to display for the text type
								echo $field['label'];
								echo '<input '. $field['disabled']. ' type="text" name="'. $field['name']. '" id="'. $field['id']. '" value="'.$field['value']. '" ' . $field['attribute'] .'/>'."<br />";
							break;
							case 'select': // The html to display for the text type
								echo $field['label'];
								echo '<select '. $field['disabled']. ' name="'. $field['name']. '" id="'. $field['id']. '" >';
								foreach( $field['options'] as $each_option ){	
									if( isset( $wp_interest_form_data[ $field['name'] ] ) && $_POST[ $field['name'] ]===$each_option['value'] ){
										$each_option['selected'] = 'selected';
									}
									echo '<option '. $each_option['selected'].' value="'.$each_option['value']. '" >' . $each_option['label']. '</option>'; 
								}
								echo '</select >';
							break;
							case 'textarea': // The html to display for the textarea type
								echo $field['label'];
								echo '<textarea name="'. $field['name']. '" id="'.$field['id']. '"placeholder="'. $field['placeholder']. '">'. $field['value']. '</textarea><br />';       
							break;
						}
					}
				}
			?>
			</div><!-- End: product interest form div -->	
			<div class="submit_my_interest_div"><input class="submit_my_interest" name="add_product_interest" id="add_product_interest" type="submit" value="<?php _e('Submit For Approval', 'TEXTDOMAIN'); ?>"></div>

</form> 