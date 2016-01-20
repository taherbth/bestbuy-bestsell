<?php
/**
 * The Header template for our theme
 *
 * Displays all of the <head> section and everything up till <div id="main">
 *
 * @package WordPress
 * @subpackage Road_Themes
 * @since Road Themes 1.0
 */
?>
<?php global $road_opt;
if(is_ssl()){
	$road_opt['logo_main']['url'] = str_replace('http:', 'https:', $road_opt['logo_main']['url']);
}
?>
		<div class="header-container layout1">
			<div class="header">
				<div class="col-xs-12 col-md-6">
					<div class="global-table">
						<div class="global-row">
							<div class="global-cell">
								<?php if( isset($road_opt['logo_main']['url']) ){ ?>
									<div class="logo"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home"><img src="<?php echo esc_url($road_opt['logo_main']['url']); ?>" alt="" /></a></div>
								<?php
								} else { ?>
									<h1 class="logo"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h1>
									<?php
								} ?>
							</div>
						</div>
					</div>
				</div>
				<div class="top-menu">	
					<div class="nav-toggler"><span class="vmn-open">
						<?php echo esc_html($road_opt['mobile_menu_label']); ?></span>
					</div>
				</div>
				<div class="col-xs-12 col-md-6">	
					<?php if ( class_exists( 'WC_Widget_Cart' ) ) {
						the_widget('Custom_WC_Widget_Cart'); 
					} ?>
					<?php if(class_exists('WC_Widget_Product_Search') ) { ?>
						<div class="header-search">
							<div class="search-icon">
								<?php the_widget('WC_Widget_Product_Search', array('title' => 'Search')); ?>
							</div>
						</div>
					<?php } ?>
					<?php if( isset($road_opt['top_menu']) ) {
						$menu_object = wp_get_nav_menu_object( $road_opt['top_menu'] );
						$menu_args = array(
							'menu_class'      => 'nav_menu',
							'menu'         => $road_opt['top_menu'],
						); ?>
						<div class="top-link widget">
							<span class="icon-links">account</span>
							<?php wp_nav_menu( $menu_args ); ?>
						</div>
					<?php } ?>

					<?php do_action('icl_language_selector'); ?>
				</div>
				<div class="visible-large">
					<div class="nav-container <?php if ( is_user_logged_in() ) { echo 'logedin'; } ?>">
						<span class="nav-close"></span>
						<?php wp_nav_menu( array( 'theme_location' => 'primary', 'container_class' => 'primary-menu-container', 'menu_class' => 'nav-menu' ) ); ?>
					</div>
				</div>
				<div class="visible-small">
					<div class="nav-container">
						<?php wp_nav_menu( array( 'theme_location' => 'mobilemenu', 'container_class' => 'mobile-menu-container', 'menu_class' => 'nav-menu' ) ); ?>
					</div>
				</div>
			</div><!-- .header -->
			<div class="clearfix"></div>
		</div>