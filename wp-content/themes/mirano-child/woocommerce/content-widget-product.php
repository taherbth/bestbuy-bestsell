<?php global $product; ?>
<div <?php post_class('item-col'); ?>>
	<div class="product-wrapper">
		<div class="list-col4">
			<div class="product-image">
				<a href="<?php echo esc_url( get_permalink( $product->id ) ); ?>" title="<?php echo esc_attr( $product->get_title() ); ?>">
					<?php 
					echo ''.$product->get_image('shop_catalog', array('class'=>'primary_image'));
					?>
				</a>
			</div>
		</div>
		<div class="list-col8">
			<h2 class="product-name">
				<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
			</h2>
			<div class="price-box"><?php echo ''.$product->get_price_html(); ?></div>
			<div class="quickviewbtn"><a class="detail-link quickview" data-quick-id="<?php the_ID();?>" href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php esc_html_e('Quick View', 'mirano');?></a></div>
		</div>
		<div class="clearfix"></div>
	</div>
</div>