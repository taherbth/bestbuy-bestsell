<?php
/**
 * The template for displaying product content in the single-product.php template
 *
 * Override this template by copying it to yourtheme/woocommerce/content-single-product.php
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>
<?php
global $road_opt, $road_secondimage;
global $interest_form, $show_this_div, $interest_start_date_time_as_text, $product_interest_validation_errors, $interest_meta_array, $format_array, $wp_interest_form_data;

?>
<?php
	/**
	 * woocommerce_before_single_product hook
	 *
	 * @hooked wc_print_notices - 10
	 */
	 do_action( 'woocommerce_before_single_product' );

	 if ( post_password_required() ) {
	 	echo get_the_password_form();
	 	return;
	 }
?>

<div itemscope itemtype="<?php echo woocommerce_get_product_schema(); ?>" id="product-<?php the_ID(); ?>" <?php post_class(); ?>>
	<div class="container">
		<div class="row">
			<div class="col-xs-12 col-md-9">
				<div class="single-product-image">
					<?php
						/**
						 * woocommerce_before_single_product_summary hook
						 *
						 * @hooked woocommerce_show_product_sale_flash - 10
						 * @hooked woocommerce_show_product_images - 20
						 */
						do_action( 'woocommerce_before_single_product_summary' );
					?>
				</div>
				<div class="summary entry-summary single-product-info">
					<?php
						/**
						 * woocommerce_single_product_summary hook
						 *
						 * @hooked woocommerce_template_single_title - 5
						 * @hooked woocommerce_template_single_rating - 10
						 * @hooked woocommerce_template_single_price - 10
						 * @hooked woocommerce_template_single_excerpt - 20
						 * @hooked woocommerce_template_single_add_to_cart - 30
						 * @hooked woocommerce_template_single_meta - 40
						 * @hooked woocommerce_template_single_sharing - 50
						 */

						do_action( 'woocommerce_single_product_summary' );
					?>

				</div><!-- .summary -->
				<?php
					/**
					 * woocommerce_after_single_product_summary hook
					 *
					 * @hooked woocommerce_output_product_data_tabs - 10
					 * @hooked woocommerce_output_related_products - 20
					 */
					do_action( 'woocommerce_after_single_product_summary' );

					/*********  Show Product Status : CMP, CIQ, Status, PPQ **********/
					do_action('inmid_product_status');
					/*********  Show interest form for a single product **********/
					do_action('bestbuy_bestsell_product_interest_form');
				?>
				<meta itemprop="url" content="<?php the_permalink(); ?>" />
				<form method="post">
					<?php
					if( $product_attributes ) {
						$num_cloms = 0;
						foreach( $product_attributes as $product_attribute ) {
							$default_select_option = 0;
							if($product_attribute){ $num_cloms++; ?>
								<div class="product_attribute_div" >
									<?php
									echo '<h4>'.$product_attribute['label'].'</h4>';
									if($product_attribute['values']){
										foreach($product_attribute['values'] as $value) {
											?>
											<label><input type="radio" name="<?php echo $product_attribute['label']; ?>" value="<?php echo $value['value']; ?>"
													<?php
													if( (strtolower( $interest_meta_array[ $product_attribute["label"] ] )  == strtolower( $value['value'] ) ) || ($default_select_option < 1 ) ){ ?> checked=checked <?php } ?> > <?php echo $value['value']; ?> </label> <br />
											<?php
											$default_select_option++;
										}
									} ?>
								</div>
								<?php if($num_cloms==3){ $num_cloms = 0; ?> <div class="clear"> </div> <?php }
							}
						}
					}

					?>
					<!--<div class="clear"> </div>-->
					<?php if ( sizeof( $product_interest_validation_errors->get_error_messages() ) <= 0 && empty( $product_interest_id ) ) 		 {
						$today_date = date('Y-m-d');
						if( !$my_interest_meta_data[0]->asa_price_is_reasonable){
							$interest_start_date_deafult = date('Y-m-d', strtotime($today_date. ' + 14 days'));
						}
						?>
						<!--<div class="clear">   </div>-->
						<div id="add_to_cart_interest_div" >
							<input class="add_to_cart_interest_div"  type="button" name="add_to_cart_interest" value="I&rsquo;m Interested" >
						</div>
					<?php } ?>
					<div class="product_interest_form_main" >
						<?php
						if ( $show_this_div ) {  /* Show this div */ ?>
						<!-- Start: product_interest_form div  -->
						<div class="product_interest_form" id="product_interest_form" style="display:block; ">
							<?php } else{ ?>
							<!-- Start: product_interest_form div  -->
							<div class="product_interest_form" id="product_interest_form" style="display:none; "> <?php }  ?>				<div class="interest_error_message">
									<?php
									if ( sizeof( $product_interest_validation_errors->get_error_messages() ) > 0 )  {
										echo '<div class="error"><p>';
										foreach ( $product_interest_validation_errors->get_error_messages($code) as $error ) {
											echo $error . "<br />";
										}
										echo '</p></div>';
									}
									?>
								</div>
								<?php
								foreach( $interest_form as $field ) {
									switch ( $field['type'] ) {
										case 'label':
											echo $field['label'].'<br />';
											break;
										case 'checkbox':
											echo '<br /><input type="checkbox" name="'. $field['name']. '" id="'. $field['id']. '"  />&nbsp;'.$field['label']."<br />";
											break;
										case 'text': // The html to display for the text type
											echo $field['label'];
											echo '<input type="text" name="'. $field['name']. '" id="'. $field['id']. '" value="'.$field['value']. '" ' . $field['attribute'] .'/>'."<br />";
											break;
										case 'select': // The html to display for the text type
											echo $field['label'];
											echo '<select name="'. $field['name']. '" id="'. $field['id']. '" >';
											foreach( $field['options'] as $each_option ){
												echo '<option value="'.$each_option['value']. '" >' . $each_option['label']. '</option>';
											}
											echo '</select >';
											break;
										case 'textarea': // The html to display for the textarea type
											echo $field['label'];
											echo '<textarea name="'. $field['name']. '" id="'.$field['id']. '"placeholder="'. $field['placeholder']. '">'. $field['value']. '</textarea><br />';
											break;
									}
								}
								?>
								<div style="margin-bottom:50px;"> </div>
							</div> <!-- End: product_interest_form div -->
						</div>
						<?php if( !$my_interest_meta_data[0]->interest_confirmed ){
							do_action('inmid_product_status_cmp_button');
							?>
							<div class="submit_my_interest_div" id="submit_my_interest_div" <?php  if ( $show_this_div ) { ?> style="display:block;" <?php } else{ ?> style="display:none;" <?php } ?> >
								<input class="submit_my_interest" name="save_interest" id="save_interest" type="submit" value="<?php if( !empty( $product_interest_id ) ){ _e('Update My Interest'); } else{_e('Save My Interest');}?>"

									   onclick='quick_view_inmid_interest( this.form, <?php echo stripslashes_deep( get_the_ID() ); ?>, "<?php echo stripslashes_deep( get_the_title() ); ?>" , <?php echo  json_encode( $product_attributes ) ; ?>) '
								>
								<input type="hidden" name="product_interest_id" value="<?php echo $product_interest_id; ?>" />
							</div>
						<?php }
						?>
				</form>

			</div>
			<div class="col-xs-12 col-md-3">
				<div id="secondary" class="sidebar-product">
					<?php do_action('woocommerce_show_related_products');?>
					<?php dynamic_sidebar( 'sidebar-product' ); ?>
				</div>
			</div>
		</div>
	</div>
	
</div><!-- #product-<?php the_ID(); ?> -->

<?php //dynamic_sidebar( 'sidebar-product' ); ?>

<?php do_action( 'woocommerce_after_single_product' ); ?>