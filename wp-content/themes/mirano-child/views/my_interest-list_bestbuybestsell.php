<?php 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
//echo get_share_buttons();
$remove_success = "";
$action_error = 0; 
$expire_error = ""; 
$user_interest_info = ""; 
$current_user_id = get_current_user_id();			
$user_info = get_userdata($current_user_id);
$all_meta_for_user = get_user_meta( $current_user_id );
//print_r($all_meta_for_user); exit;
if($user_info->roles){
	$current_user_role = implode(', ', $user_info->roles) ;
}

if( isset( $_REQUEST['action'] ) && $_REQUEST['action']!="" ){
	$product_interest_id = $_REQUEST['product_interest_id'];
	$valid_action = wp_check_valid_user_action( $current_user_id, $product_interest_id, "product_interest"); 
	if( $valid_action && $_REQUEST['action']=="remove" ){
		$my_interest_meta_data = wp_my_interest_meta( $current_user_id, $product_interest_id );
		if ( !$my_interest_meta_data[0]->interest_confirmed  ){
				$remove_success = wp_remove_interest( $current_user_id, $product_interest_id );		
		}else{
			echo '<div class="notice">Sorry!!! You Don&rsquo;t Have Permission To This Action As You Already Confirmed Your Purchase Interest</div>';
		}				
	}elseif( $valid_action && $_REQUEST['action']=="edit"){ 
		$product_name = $_REQUEST['product_name'];
		wp_redirect( get_site_url().'/product/'.$product_name.'/?product_interest_id='.$product_interest_id, 301 ); exit;
		//$remove_success = wp_remove_interest( $current_user_id, $product_interest_id );		
	}elseif( $valid_action && $_REQUEST['action']=="interest_confirmed"){
		$user_interest_info = user_product_interest_details( $product_interest_id );
		$interest_confirmation_link_expire = $user_interest_info[0]->interest_confirmation_link_expire; 
		if( $user_interest_info[0]->interest_confirmation_link_expire ){
			$time_now = mktime( date('H'), date('i'), 0, date('m'),date('d'),date('Y')	); 
			if( $interest_confirmation_link_expire >= $time_now ){
				$wp_product_interest_data = array( "interest_confirmed" => 1 ); 
				$format_array = array("%d");
				$confirmed_success = wp_product_interest_update( $wp_product_interest_data, $format_array, $product_interest_id );
			}else{ $expire_error = _e("Sorry!!! Your Purchase Confirmation Time expired");  }
		}else{ $expire_error = _e("Sorry!!! Your Purchase Confirmation Time Not Set Yet"); }		
	}else{
		$action_error = 1; 
	}
}
		
//$product_attributes = get_field('attributes');
$product_attributes = array();
$my_interest_list = wp_my_interest_list( $current_user_id );

?>
		<!-- MAIN CONTENT -->	  	
					<div id="yith-wcwl-messages"><?php if( $remove_success ){ _e("Your Interest Removed successfully!!"); } 
						if( $confirmed_success ){ 
							_e("You Just Confirmed Your Purchase Interest For The Product: "); 
							echo $user_interest_info[0]->post_title;
						} 
						if( $action_error ){ _e("Sorry!!! You don't have permision to this action!!!"); } 
						if( $expire_error ){ echo $expire_error; } 					
					//echo Wsi_Public::widget( 'Share My Friend'); ?>										
					</div>
					<?php if($my_interest_list) { ?>
						<form method="post" action="" id="yith-wcwl-form">
							<table cellspacing="0" class="shop_table wishlist_table">
								<thead>
								<tr>
									<th class="product-no">#</th>
									<th class="product-thumbnail"><?php _e( 'Image', TEXTDOMAIN ); ?> </th>
									<th class="product-name"><span class="nobr"> <?php _e( 'Product Name', TEXTDOMAIN ); ?> </span></th>
									<th class="product-price"><span class="nobr"><?php _e( 'Qty', TEXTDOMAIN ); ?></span></th> 
									<th><span class="nobr"><?php _e( 'Start Date', TEXTDOMAIN ); ?></span></th>  
									<th><span class="nobr"><?php _e( 'End Date', TEXTDOMAIN ); ?></span></th>  
									<th><span class="nobr"><?php _e( 'My Stats', TEXTDOMAIN ); ?></span></th>  
									<th><span class="nobr"><?php _e( 'Action', TEXTDOMAIN ); ?></span></th>  
								</tr>
								</thead>								
								<tbody>
									<?php 									
										$num_cloms = 0;
										foreach( $my_interest_list as $my_interest_data ) {
											if($my_interest_data){ 
												$num_cloms++; 
												//$post_thumbnail_id = get_post_thumbnail_id( $my_interest_data->ID ); 
												$post_thumbnail = get_the_post_thumbnail( $my_interest_data->ID, 'thumbnail' );
									?>
									<tr id="yith-wcwl-row-7">
										<td class="product-no">
											<span class="product-amount"><?php echo $num_cloms; ?> </span>										
										</td>
										<td class="product-thumbnail">
										<a href="<?php echo get_site_url().'/product/'.$my_interest_data->post_name; ?>"  > <?php echo $post_thumbnail; ?>
										</a>
										</td>
										<td class="product-name">
										<a href="<?php echo get_site_url().'/product/'.$my_interest_data->post_name; ?>"  > <?php echo $my_interest_data->product_name; ?></a>										
										
										</td>
										<td class="product-qty">
											<span class="amount"><?php echo $my_interest_data->interest_qty; ?></span>                           
										</td>
										<td class="product-start-date">
											<span class="start-date">
											<?php if( $my_interest_data->interest_start_date ){ echo date( "Y-m-d", $my_interest_data->interest_start_date ); }elseif( $my_interest_data->asa_price_is_reasonable ){ _e("As soon as price is reasonable"); } ?>
											</span> 
										</td>
										<td class="product-end-date">
										<span class="end-date">
										<?php if( $my_interest_data->interest_end_date ){ echo date( "Y-m-d", $my_interest_data->interest_end_date ); } ?>
										</span> 
										</td>
										<td class="product-status">
										<?php 
											/*if( $my_interest_data->post_status!='publish' ){ 
												_e('Waiting For Approval');					
											}else{*/ 
												do_action('my_interest_stats', $my_interest_data->product_interest_id, $my_interest_data->post_status );	
										//} 
										?>										
										</td>
										<td class="product-status">
										<?php if( $my_interest_data->post_status==='publish' && !$my_interest_data->interest_confirmed ){ ?>
											<span ><a href="#?action=edit&product_interest_id=<?php echo $my_interest_data->product_interest_id; ?>&product_name=<?php echo $my_interest_data->post_name; ?>"  class="edit-icon"> </a></span> 
											<span ><a href="?action=remove&product_interest_id=<?php echo $my_interest_data->product_interest_id; ?>"  class="remove"> </a></span> 
										<?php }  ?>
										</td>											
									</tr>			
									<?php } }  ?>
								</tbody>
							</table>
					</form>
					<?php }  else _e("You don't have any interest");?>

		<!-- //MAIN CONTENT -->