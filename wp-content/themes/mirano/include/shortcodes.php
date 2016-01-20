<?php
//Shortcodes for Visual Composer

add_action( 'vc_before_init', 'road_vc_shortcodes' );
function road_vc_shortcodes() {
	
	//Brand logos
	vc_map( array(
		"name" => esc_html__( "Brand Logos", "mirano" ),
		"base" => "ourbrands",
		"class" => "",
		"category" => esc_html__( "RoadThemes", "mirano"),
		"params" => array(
			array(
				"type" => "dropdown",
				"holder" => "div",
				"class" => "",
				"heading" => esc_html__( "Number of rows", "mirano" ),
				"param_name" => "rowsnumber",
				"value" => array(
						'one'	=> 'one',
						'three'	=> 'three',
					),
			),
		)
	) );
	
	//Latest posts
	vc_map( array(
		"name" => esc_html__( "Latest posts", "mirano" ),
		"base" => "latestposts",
		"class" => "",
		"category" => esc_html__( "RoadThemes", "mirano"),
		"params" => array(
			array(
				"type" => "textfield",
				"holder" => "div",
				"class" => "",
				"heading" => esc_html__( "Number of posts", "mirano" ),
				"param_name" => "posts_per_page",
				"value" => esc_html__( "5", "mirano" ),
			),
			array(
				"type" => "dropdown",
				"holder" => "div",
				"class" => "",
				"heading" => esc_html__( "Image scale", "mirano" ),
				"param_name" => "image",
				"value" => array(
						'Wide'	=> 'wide',
						'Square'	=> 'square',
					),
			),
			array(
				"type" => "textfield",
				"holder" => "div",
				"class" => "",
				"heading" => esc_html__( "Excerpt length", "mirano" ),
				"param_name" => "length",
				"value" => esc_html__( "20", "mirano" ),
			),
		)
	) );
	
	//Testimonials
	vc_map( array(
		"name" => esc_html__( "Testimonials", "mirano" ),
		"base" => "woothemes_testimonials",
		"class" => "",
		"category" => esc_html__( "RoadThemes", "mirano"),
		"params" => array(
			array(
				"type" => "textfield",
				"holder" => "div",
				"class" => "",
				"heading" => esc_html__( "Number of testimonial", "mirano" ),
				"param_name" => "limit",
				"value" => esc_html__( "10", "mirano" ),
			),
			array(
				"type" => "textfield",
				"holder" => "div",
				"class" => "",
				"heading" => esc_html__( "Image size", "mirano" ),
				"param_name" => "size",
				"value" => esc_html__( "120", "mirano" ),
			),
		)
	) );
	
	//Skill
	vc_map( array(
		"name" => esc_html__( "Skill", "mirano" ),
		"base" => "skill",
		"class" => "",
		"category" => esc_html__( "RoadThemes", "mirano"),
		"params" => array(
			array(
				"type" => "textfield",
				"holder" => "div",
				"class" => "",
				"heading" => esc_html__( "Skill title", "mirano" ),
				"param_name" => "title",
				"value" => esc_html__( "Skill", "mirano" ),
			),
			array(
				"type" => "textfield",
				"holder" => "div",
				"class" => "",
				"heading" => esc_html__( "Percent", "mirano" ),
				"param_name" => "percent",
				"value" => esc_html__( "100", "mirano" ),
			),
		)
	) );
	
	//Rotating tweets
	vc_map( array(
		"name" => esc_html__( "Rotating tweets", "mirano" ),
		"base" => "rotatingtweets",
		"class" => "",
		"category" => esc_html__( "RoadThemes", "mirano"),
		"params" => array(
			array(
				"type" => "textfield",
				"holder" => "div",
				"class" => "",
				"heading" => esc_html__( "Twitter user name", "mirano" ),
				"param_name" => "screen_name",
				"value" => esc_html__( "RoadThemes", "mirano" ),
			),
		)
	) );
	
	//Icons
	vc_map( array(
		"name" => esc_html__( "FontAwesome Icon", "mirano" ),
		"base" => "roadicon",
		"class" => "",
		"category" => esc_html__( "RoadThemes", "mirano"),
		"params" => array(
			array(
				"type" => "textfield",
				"holder" => "",
				"class" => "",
				"heading" => esc_html__( "FontAwesome Icon", "mirano" ),
				"description" => esc_html__( "<a href=\"http://fortawesome.github.io/Font-Awesome/cheatsheet/\" target=\"_blank\">Go here</a> to get icon class. Example: fa-search", "mirano" ),
				"param_name" => "icon",
				"value" => esc_html__( "fa-search", "mirano" ),
			),
		)
	) );
	//Mail Poet form
	vc_map( array(
		"name" => esc_html__( "Newsletter", "mirano" ),
		"base" => "wysija_form",
		"class" => "",
		"category" => esc_html__( "RoadThemes", "mirano"),
		"params" => array(
			array(
				"type" => "textfield",
				"holder" => "div",
				"class" => "",
				"heading" => esc_html__( "Newsletter Form ID", "mirano" ),
				"param_name" => "id",
				"value" => esc_html__( "1", "mirano" ),
			),
		)
	) );
	
}
?>