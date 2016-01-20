<?php
/**
 * The template for displaying 404 pages (Not Found)
 *
 * @package WordPress
 * @subpackage Road_Themes
 * @since Road Themes 1.0
 */

global $road_opt;

get_header();

if(is_ssl()){
	$road_opt['image_error']['url'] = str_replace('http:', 'https:', $road_opt['image_error']['url']);
}

?>
	<div class="main-container error404">
		<?php if( isset($road_opt['image_error']['url']) ){ ?>
			<div class="image-404"><img src="<?php echo esc_url($road_opt['image_error']['url']); ?>" alt="" /></div>
		<?php } ?>
		<div class="search-form-wrapper">
			<h2><span><?php esc_html_e( "Oops ! That Page Can't Be Found.", "mirano" ); ?></span></h2>
			<label><?php esc_html_e("Can't find what you need? Take a moment and do a search below!", "mirano");?></label>
			<?php get_search_form(); ?>
		</div>
		
	</div>

<?php get_footer(); ?>