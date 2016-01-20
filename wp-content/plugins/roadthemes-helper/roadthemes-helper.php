<?php
/**
 * Plugin Name: RoadThemes Helper
 * Plugin URI: http://roadthemes.com/
 * Description: The helper plugin for RoadThemes themes.
 * Version: 1.0.0
 * Author: RoadThemes
 * Author URI: http://roadthemes.com/
 * Text Domain: roadthemes
 * License: GPL/GNU.
 /*  Copyright 2014  RoadThemes  (email : support@roadthemes.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
// Add shortcodes
function road_skill_shortcode( $atts ) {
	$atts = shortcode_atts( array(
		'title' => 'Skill',
		'percent' => '100'
	), $atts, 'skill' );
	
	$html = '<div class="skill-wrapper">';
	$html.= '<div class="percent"><div class="percent_color" style="width: '.$atts['percent'].'%;"></div></div>';
	$html.= '<div class="percent_text">'.$atts['title'].'<span>'.$atts['percent'].'%</span></div>';
	$html.= '</div>';
	
	return $html;
}
add_shortcode( 'skill', 'road_skill_shortcode' );

function road_brands_shortcode( $atts ) {
	global $road_opt;
	$brand_index = 0;
	$brandfound=count($road_opt['brand_logos']);

	$atts = shortcode_atts( array('rowsnumber' => '1'), $atts, 'ourbrands' );

	$rowsnumber = $atts['rowsnumber'];

	$html = '';
	
	if($road_opt['brand_logos']) {
		$html .= '<div class="brands-carousel rows-'.$rowsnumber.'">';
			foreach($road_opt['brand_logos'] as $brand) {
				if(is_ssl()){
					$brand['image'] = str_replace('http:', 'https:', $brand['image']);
				}
				$brand_index ++;
				
				switch ($rowsnumber) {
					case "one":
						$html .= '<div class="group">';
						break;
					case "three":
						if ( (0 == ( $brand_index - 1 ) % 3 ) || $brand_index == 1) {
							$html .= '<div class="group">';
						}
						break;
				}
				
				$html .= '<div class="brands-inner">';
				$html .= '<a href="'.$brand['url'].'" title="'.$brand['title'].'">';
					$html .= '<img src="'.$brand['image'].'" alt="'.$brand['title'].'" />';
				$html .= '</a>';
				$html .= '</div>';
				
				switch ($rowsnumber) {
					case "one":
						$html .= '</div>';
						break;
					case "three":
						if ( ( ( 0 == $brand_index % 3 || $brandfound == $brand_index ))  ) { /* for odd case: $road_productsfound == $woocommerce_loop['loop'] */
							$html .= '</div>';
						}
						break;
				}

			}
		$html .= '</div>';
	}
	
	return $html;
}
add_shortcode( 'ourbrands', 'road_brands_shortcode' );

function road_latestposts_shortcode( $atts ) {
	global $road_opt;
	
	$k = 0;
	$atts = shortcode_atts( array(
		'posts_per_page' => 5,
		'order' => 'DESC',
		'orderby' => 'post_date',
		'image' => 'wide', //square
		'length' => 20
	), $atts, 'latestposts' );

	
	if($atts['image']=='wide'){
		$imagesize = 'mirano-post-thumbwide';
	} else {
		$imagesize = 'mirano-post-thumb';
	}
	$html = '';
	$postinfocode ='';
	$postthumbcode ='';

	$postargs = array(
		'posts_per_page'   => $atts['posts_per_page'],
		'offset'           => 0,
		'category'         => '',
		'category_name'    => '',
		'orderby'          => $atts['orderby'],
		'order'            => $atts['order'],
		'include'          => '',
		'exclude'          => '',
		'meta_key'         => '',
		'meta_value'       => '',
		'post_type'        => 'post',
		'post_mime_type'   => '',
		'post_parent'      => '',
		'post_status'      => 'publish',
		'suppress_filters' => true );
	
	$postslist = get_posts( $postargs );

	$html.='<div class="posts-carousel">';

			foreach ( $postslist as $post ) {
				$k = 1 - $k;
				$html.='<div class="item-col">';
					$html.='<div class="post-wrapper">';
						$html.='<div class="post-wrapper-inner">';
							$postthumbcode.='<div class="post-thumb">';
								$postthumbcode.='<div class="post-img">';
									$postthumbcode.='<a href="'.get_the_permalink($post->ID).'">'.get_the_post_thumbnail($post->ID, $imagesize);
									
									$postthumbcode.='<span class="post-date3"><span class="day">'.get_the_date('d', $post->ID).'</span><span class="month">'.get_the_date('M', $post->ID).'</span></span>';
									$postthumbcode.='<span class="shadow"></span>';
									$postthumbcode.='</a>';
									$postthumbcode.='<div class="post-meta ontop">';
									
									$num_comments = get_comments_number($post->ID);
										if ( comments_open() ) {
											if ( $num_comments == 0 ) {
												$comments = __('<span>0</span> comments', 'roadthemes');
											} elseif ( $num_comments > 1 ) {
												$comments = '<span>'.$num_comments .'</span>'. __(' comments', 'roadthemes');
											} else {
												$comments = __('<span>1</span> comment', 'roadthemes');
											}
											$write_comments = '<a href="' . get_comments_link($post->ID) .'">'. $comments.'</a>';
										}
										
										$postthumbcode.='<span class="entry-date"><span class="day">'.get_the_date('d', $post->ID).'</span><span class="month">'.get_the_date('M', $post->ID).'</span><span class="year">'.get_the_date(',Y', $post->ID).'</span></span>';

										$categories_list = get_the_category_list( ',','', $post->ID );

										$author = sprintf( '<span class="author vcard"><a class="url fn n" href="%1$s" title="%2$s" rel="author">%3$s</a></span>',
											esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
											esc_attr( sprintf( __( 'View all posts by %s', 'roadthemes' ), get_the_author() ) ),
											get_the_author()
										);
										
									$postthumbcode.='</div>';

								$postthumbcode.='</div>';

							$postthumbcode.='</div>';
							$postinfocode.='<div class="post-info">';
								
								$postinfocode.='<h3 class="post-title"><a href="'.get_the_permalink($post->ID).'">'.get_the_title($post->ID).'</a></h3>';
								$postinfocode.='<span class="post-date"><span class="day">'.get_the_date('d.', $post->ID).'</span><span class="month">'.get_the_date('m.', $post->ID).'</span><span class="year">'.get_the_date('Y', $post->ID).'</span></span>';
								$postinfocode.='<span class="entry-comment">'. $write_comments.'</span>';
								$postinfocode.='<div class="post-excerpt">';
									$postinfocode.=road_excerpt_by_id($post, $length = $atts['length']);
								$postinfocode.='</div>';
								$postinfocode.='<a class="readmore" href="'.get_the_permalink($post->ID).'">'.'<span>' .esc_html($road_opt['readmore_text']). '</span>'.'</a>';

								$postinfocode.='<ul class="list-infor">';	
									$postinfocode.='<li class="first"><span class="author">'. $author.'</span></li>';
									$postinfocode.='<li><span class="category">'. $categories_list.'</span></li>';
									$postinfocode.='<li class="last"><span class="entry-comment">'. $write_comments.'</span></li>';
								$postinfocode.='</ul>';							
								
							$postinfocode.='</div>';

							if($k==0) {
								$html.=$postthumbcode;
								$html.=$postinfocode;
							} else {
								$html.=$postinfocode;
								$html.=$postthumbcode;
							}
							$postinfocode ='';
							$postthumbcode ='';

						$html.='</div>';		
					$html.='</div>';
				$html.='</div>';

			}
	$html.='</div>';

	wp_reset_postdata();
	
	return $html;
}
add_shortcode( 'latestposts', 'road_latestposts_shortcode' );

function road_popular_category_shortcode( $atts ) {

	$atts = shortcode_atts( array(
		'category' => '',
		'image' => ''
	), $atts, 'popular_category' );
	
	$html = '';
	
	$html .= '<div class="category-wrapper">';
		$pcategory = get_term_by( 'slug', $atts['category'], 'product_cat', 'ARRAY_A' );
		if($pcategory){
			$html .= '<div class="category-list">';
				$html .= '<h3><a href="'. get_term_link($pcategory['slug'], 'product_cat') .'">'. $pcategory['name'] .'</a></h3>';
				
				$html .= '<ul>';
					$args2 = array(
						'taxonomy'     => 'product_cat',
						'child_of'     => 0,
						'parent'       => $pcategory['term_id'],
						'orderby'      => 'name',
						'show_count'   => 0,
						'pad_counts'   => 0,
						'hierarchical' => 0,
						'title_li'     => '',
						'hide_empty'   => 0
					);
					$sub_cats = get_categories( $args2 );

					if($sub_cats) {
						foreach($sub_cats as $sub_category) {
							$html .= '<li><a href="'.get_term_link($sub_category->slug, 'product_cat').'">'.$sub_category->name.'</a></li>';
						}
					}
				$html .= '</ul>';
			$html .= '</div>';

			if ($atts['image']!='') {
			$html .= '<div class="cat-img">';
				$html .= '<a href="'.get_term_link($pcategory['slug'], 'product_cat').'"><img class="category-image" src="'.esc_attr($atts['image']).'" alt="" /></a>';
			$html .= '</div>';
			}
		}
	$html .= '</div>';
	
	return $html;
}
add_shortcode( 'popular_category', 'road_popular_category_shortcode' );

function road_icon_shortcode( $atts ) {
	$atts = shortcode_atts( array(
		'icon' => ''
	), $atts, 'roadicon' );
	
	$html = '<i class="fa '.$atts['icon'].'"></i>';
	
	
	return $html;
}
add_shortcode( 'roadicon', 'road_icon_shortcode' );

//Add less compiler
function compileLessFile($input, $output, $params) {
    // include lessc.inc
    require_once( plugin_dir_path( __FILE__ ).'less/lessc.inc.php' );
	
	$less = new lessc;
	$less->setVariables($params);
	
    // input and output location
    $inputFile = get_template_directory().'/less/'.$input;
    $outputFile = get_template_directory().'/css/'.$output;

    $less->compileFile($inputFile, $outputFile);
}

function road_excerpt_by_id($post, $length = 10, $tags = '<a><em><strong>') {
 
	if(is_int($post)) {
		// get the post object of the passed ID
		$post = get_post($post);
	} elseif(!is_object($post)) {
		return false;
	}
 
	if(has_excerpt($post->ID)) {
		$the_excerpt = $post->post_excerpt;
		return apply_filters('the_content', $the_excerpt);
	} else {
		$the_excerpt = $post->post_content;
	}
 
	$the_excerpt = strip_shortcodes(strip_tags($the_excerpt), $tags);
	$the_excerpt = preg_split('/\b/', $the_excerpt, $length * 2+1);
	$excerpt_waste = array_pop($the_excerpt);
	$the_excerpt = implode($the_excerpt);
 
	return apply_filters('the_content', $the_excerpt);
}

function road_blog_sharing() {
	global $post, $road_opt;
	
	$share_url = get_permalink( $post->ID );
	$large_image_url = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'large' );
	$postimg = $large_image_url[0];
	$posttitle = get_the_title( $post->ID );
	?>
	<div class="widget widget_socialsharing_widget">
		<h3 class="widget-title"><?php if(isset($road_opt['blog_share_title'])) { echo esc_html($road_opt['blog_share_title']); } else { _e('Share this post', 'roadthemes'); } ?></h3>
		<ul class="social-icons">
			<li><a class="facebook social-icon" href="#" onclick="javascript: window.open('<?php echo 'https://www.facebook.com/sharer/sharer.php?u='.$share_url; ?>'); return false;" title="Facebook" target="_blank"><i class="fa fa-facebook"></i></a></li>
			<li><a class="twitter social-icon" href="#" title="Twitter" onclick="javascript: window.open('<?php echo 'https://twitter.com/home?status='.$posttitle.'&nbsp;'.$share_url; ?>'); return false;" target="_blank"><i class="fa fa-twitter"></i></a></li>
			<li><a class="pinterest social-icon" href="#" onclick="javascript: window.open('<?php echo 'https://pinterest.com/pin/create/button/?url='.$share_url.'&amp;media='.$postimg.'&amp;description='.$posttitle; ?>'); return false;" title="Pinterest" target="_blank"><i class="fa fa-pinterest"></i></a></li>
			<li><a class="gplus social-icon" href="#" onclick="javascript: window.open('<?php echo 'https://plus.google.com/share?url='.$share_url; ?>'); return false;" title="Google +" target="_blank"><i class="fa fa-google-plus"></i></a></li>
			<li><a class="linkedin social-icon" href="#" onclick="javascript: window.open('<?php echo 'https://www.linkedin.com/shareArticle?mini=true&amp;url='.$share_url.'&amp;title='.$posttitle; ?>'); return false;" title="LinkedIn" target="_blank"><i class="fa fa-linkedin"></i></a></li>
		</ul>
	</div>
	<?php
}

function road_product_sharing() {
	global $road_opt;
	
	if(isset($_POST['data'])) { // for the quickview
		$postid = intval( $_POST['data'] );
	} else {
		$postid = get_the_ID();
	}
	
	$share_url = get_permalink( $postid );

	$large_image_url = wp_get_attachment_image_src( get_post_thumbnail_id( $postid ), 'large' );
	$postimg = $large_image_url[0];
	$posttitle = get_the_title( $postid );
	?>
	<div class="widget widget_socialsharing_widget">
		<h3 class="widget-title"><?php if(isset($road_opt['product_share_title'])) { echo esc_html($road_opt['product_share_title']); } else { _e('Share this product', 'roadthemes'); } ?></h3>
		<ul class="social-icons">
			<li><a class="facebook social-icon" href="#" onclick="javascript: window.open('<?php echo 'https://www.facebook.com/sharer/sharer.php?u='.$share_url; ?>'); return false;" title="Facebook" target="_blank"><i class="fa fa-facebook"></i></a></li>
			<li><a class="twitter social-icon" href="#" title="Twitter" onclick="javascript: window.open('<?php echo 'https://twitter.com/home?status='.$posttitle.'&nbsp;'.$share_url; ?>'); return false;" target="_blank"><i class="fa fa-twitter"></i></a></li>
			<li><a class="pinterest social-icon" href="#" onclick="javascript: window.open('<?php echo 'https://pinterest.com/pin/create/button/?url='.$share_url.'&amp;media='.$postimg.'&amp;description='.$posttitle; ?>'); return false;" title="Pinterest" target="_blank"><i class="fa fa-pinterest"></i></a></li>
			<li><a class="gplus social-icon" href="#" onclick="javascript: window.open('<?php echo 'https://plus.google.com/share?url='.$share_url; ?>'); return false;" title="Google +" target="_blank"><i class="fa fa-google-plus"></i></a></li>
			<li><a class="linkedin social-icon" href="#" onclick="javascript: window.open('<?php echo 'https://www.linkedin.com/shareArticle?mini=true&amp;url='.$share_url.'&amp;title='.$posttitle; ?>'); return false;" title="LinkedIn" target="_blank"><i class="fa fa-linkedin"></i></a></li>
		</ul>
	</div>
	<?php
}