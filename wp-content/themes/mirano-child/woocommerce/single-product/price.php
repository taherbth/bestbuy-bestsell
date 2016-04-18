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

?>
<div class="price-box" itemprop="offers" itemscope itemtype="http://schema.org/Offer">
	<p class="price"><?php _e('CIQ'); echo ": ".$product->get_price_html(); ?></p>
</div>
