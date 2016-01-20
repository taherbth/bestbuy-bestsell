<?php
/**
 * The template for displaying posts in the Image post format
 *
 * Used for both single and index/archive/search.
 *
 * @package WordPress
 * @subpackage Road_Themes
 * @since Road Themes 1.0
 */
global $road_opt, $road_postthumb;

?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<div class="post-wrapper">
	<?php if ( ! post_password_required() && ! is_attachment() ) : ?>
		<?php if ( has_post_thumbnail() ) { ?>
			<div class="post-thumbnail">
				<?php if ( is_single() ) { ?>
					<?php the_post_thumbnail(); ?>
				
				<?php } else { ?>
					<?php the_post_thumbnail($road_postthumb); ?>
					
				<?php } ?>
			</div>
		<?php } ?>
	<?php endif; ?>
	
	<div class="postinfo-wrapper">
		<div class="post-info <?php if ( !has_post_thumbnail() ) { echo 'no-thumbnail';} ?>">

			<header class="entry-header">
				<?php if ( is_single() ) : ?>
				<?php echo '<span class="entry-date"><span class="day">'.get_the_date('d', $post->ID).'</span><span class="month">'.get_the_date('M', $post->ID).'</span></span>' ;?>
					<h1 class="entry-title"><?php the_title(); ?></h1>
				<?php else : ?>
					<h1 class="entry-title">
						<a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a>
					</h1>
					<?php Mirano_Theme::road_entry_meta_small(); ?>
				<?php endif; ?>
			</header>
			
			<?php if ( is_single() ) : ?>
				<div class="entry-meta">
					<?php Mirano_Theme::road_entry_meta(); ?>
				</div>
			<?php endif; ?>
			
			<?php if ( is_single() ) : ?>
				<div class="entry-content">
					<?php the_content( esc_html__( 'Continue reading <span class="meta-nav">&rarr;</span>', 'mirano' ) ); ?>
					<?php wp_link_pages( array( 'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'mirano' ), 'after' => '</div>', 'pagelink' => '<span>%</span>' ) ); ?>
				</div>
			<?php else : ?>
				<div class="entry-summary">
					<?php the_excerpt(); ?>
					<a class="readmore" href="<?php the_permalink(); ?>"><?php if(isset($road_opt)){ echo esc_html($road_opt['readmore_text']); } else { esc_html_e('Read more', 'mirano');}  ?></a>
				</div>
			<?php endif; ?>
			
			<?php if ( is_single() ) : ?>
				<?php if( function_exists('road_blog_sharing') ) { ?>
					<div class="social-sharing"><?php road_blog_sharing(); ?></div>
				<?php } ?>
			
				<div class="author-info">
					<div class="author-avatar">
						<?php
						$author_bio_avatar_size = apply_filters( 'roadthemes_author_bio_avatar_size', 68 );
						echo get_avatar( get_the_author_meta( 'user_email' ), $author_bio_avatar_size );
						?>
					</div>
					<div class="author-description">
						<h2><?php printf( wp_kses(__( 'About the Author: <a href="'.esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ).'" rel="author">%s</a>', 'mirano' ), array('a'=>array('href'=>array(), 'rel'=>array()))), get_the_author() ); ?></h2>
						<p><?php the_author_meta( 'description' ); ?></p>
					</div>
				</div>
			<?php endif; ?>
			
		</div>
	</div>
	</div>
</article>