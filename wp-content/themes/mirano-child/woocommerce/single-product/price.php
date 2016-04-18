<?php
/**
 * Single Product Price, including microdata for SEO
 *
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 2.4.9
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $product;
/*********  Show Product Status : CMP, CIQ, Status, PPQ **********/
do_action('bestbuy_bestsell_product_status');
global $ciq_to_display_visitor;
?>

<div class="price-box" itemprop="offers" itemscope itemtype="http://schema.org/Offer">
	<p class="price">
		<a id="tool_tip_design" class="tooltip_product_ciq" title="<?php _e('Current Interest Quantity', TEXTDOMAIN);?>" >
		<?php _e('CIQ'); echo ": ".$ciq_to_display_visitor; ?></a></p>
</div>
