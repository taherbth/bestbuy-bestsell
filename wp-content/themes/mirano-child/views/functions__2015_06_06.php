<?php

remove_action( 'wp_head', 'et_add_viewport_meta', 10 );

//INITS
remove_action( 'wp_head', 'et_divi_add_customizer_css');

define('CODEDROP_DIR', get_stylesheet_directory_uri());

add_action('init', function() {
  remove_action('et_header_top', 'et_add_mobile_navigation');
});

add_shortcode('products_list', 'codedrop_shortcode_products');

function codedrop_shortcode_products(){
	$output .= '<input class="products-searchform" placeholder="T.ex. iPhone 6 eller Nikon D3200" type="searchform" />';
	$output .= '<div class="units-row products-wrap">
		<div class="unit-20 product-category">
			<h4>Datorer & delar</h4>
			<p>65 343 st</p>
		</div><!-- end .product-category -->
		<div class="unit-20 product-category">
			<h4>Spel & konsoller</h4>
			<p>65 343 st</p>
		</div><!-- end .product-category -->
		<div class="unit-20 product-category">
			<h4>Foto & video</h4>
			<p>65 343 st</p>
		</div><!-- end .product-category -->
		<div class="unit-20 product-category">
			<h4>Ljud & bild</h4>
			<p>65 343 st</p>
		</div><!-- end .product-category -->
		<div class="unit-20 product-category">
			<h4>Telefoni & GPS</h4>
			<p>65 343 st</p>
		</div><!-- end .product-category -->
	</div>';
	
	return $output;
}

add_shortcode('search-form', 'codedrop_shortcode_search_form');

function codedrop_shortcode_search_form(){
	$serach_box = '<input type="search" name="s" />';
	//return SearchWP_Live_Search_Form::get_search_form($serach_box);
	ob_start();
	echo the_widget('SearchWP_Live_Search_Widget');
	$output = ob_get_contents();
    ob_end_clean();
    
    return $output;
}

add_shortcode('all_my_interest', 'all_my_interest_shortcode');

function all_my_interest_shortcode(){
	ob_start();
	echo get_template_part('woocommerce/my-interest/my_interest', 'list_inmid');
	$output = ob_get_contents();
    ob_end_clean();
    return $output;
}

add_shortcode('register-form', 'codedrop_shortcode_register_form');

function codedrop_shortcode_register_form(){
	return '<form class="register-form" method="post"><input placeholder="E-mail" type="email" /><input placeholder="Lösenord" type="password" /><input type="submit" value="Skapa konto" /></form>';
}

function gen_mobile_menu() {
?>
  <div class="gen-mobile-nav-menu">
    <a href="#" class="menu-icon"></a>
  </div>
  <?php
}

add_action('wp_footer', function() {
	?>
 	<nav class="mobile-side-menu">
      <?php echo wp_nav_menu( array( 'theme_location' => 'primary-menu', 'container' => '', 'fallback_cb' => '', 'menu-id' => 'gen-mobile-menu', 'menu_class' => 'gen-mobile-menu', 'echo' => false ) ); ?>
    </nav>
    <?php
});

add_action('et_header_top', 'gen_mobile_menu');

add_shortcode('lightbox-button', 'codedrop_lightbox_button');

function codedrop_lightbox_button($atts, $content = null) {
	extract( shortcode_atts( array(
			'title' => 'Open Lightbox',
			'class' => ''
		), $atts
	) );
	
	return '<div class="lightbox-button '.$class.'"><a href="#">'.$title.'</a><div style="display: none;" class="lightbox-button-content">'.do_shortcode($content).'</div></div>';
	
}

add_shortcode('toggle', 'codedrop_toggle_shortcode');

function codedrop_toggle_shortcode( $atts, $content = null ) {
	extract( shortcode_atts( array(
			'title' => 'Toggle',
			'width' => '500px',
			'class' => ''
		), $atts
	) );
		return '<a class="toggle-button '.$class.'" href="#">'.$title.'</a><div style="max-height: 0; overflow-y: hidden; text-align: center; margin: 20px auto 0; -webkit-transition-property: all; -webkit-transition-duration: .5s; -webkit-transition-timing-function: ease-in-out; -moz-transition-property: all; -moz-transition-duration: .5s; -moz-transition-timing-function: ease-in-out; -ms-transition-property: all; -ms-transition-duration: .5s; -ms-transition-timing-function: ease-in-out; transition-property: all; transition-duration: .5s; transition-timing-function: ease-in-out; width: '.$width.';" class="toggle-content">' . do_shortcode($content) . '</div>';
	
}

function codedrop_scripts() {
	wp_enqueue_style( 'stylable-select-style', CODEDROP_DIR . '/init/css/select-theme-default.css' );
	wp_enqueue_style( 'datepicker-style', CODEDROP_DIR . '/init/css/datepicker3.css' );
	wp_enqueue_script( 'jquery', '//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js' );
	//wp_enqueue_script( 'datepicker-script', CODEDROP_DIR . '/init/js/bootstrap-datepicker.js', array('jquery') );
	//wp_enqueue_script( 'datepicker-script-se', CODEDROP_DIR . '/init/js/bootstrap-datepicker.sv.js', array('jquery', 'datepicker-script') );
	wp_enqueue_script( 'general-scripts', CODEDROP_DIR . '/init/js/scripts.js', array('jquery') );
	wp_enqueue_script( 'select-min-js',  CODEDROP_DIR . '/init/js/select.min.js', array('jquery'));
	wp_enqueue_style( 'codedrop-style', CODEDROP_DIR . '/style.css' );
}

//add_action( 'wp_enqueue_scripts', 'codedrop_scripts' );

/* include_once('NinjaForms/fields/recaptcha-no-robot.php'); */

add_filter('body_class','add_codedrop_custom_body_class'); 

function add_codedrop_custom_body_class($classes = '') {
	
	global $is_lynx, $is_gecko, $is_IE, $is_opera, $is_NS4, $is_safari, $is_chrome, $is_iphone;

	if($is_lynx) $classes[] = 'lynx';
	elseif($is_gecko) $classes[] = 'gecko';
	elseif($is_opera) $classes[] = 'opera';
	elseif($is_NS4) $classes[] = 'ns4';
	elseif($is_safari) $classes[] = 'safari';
	elseif($is_chrome) $classes[] = 'chrome';
	elseif($is_IE) $classes[] = 'ie';
	else $classes[] = 'unknown';

	if($is_iphone) $classes[] = 'iphone';
	if(is_page_template()) {
		$classes[] = 'no-padding-top';
	}
	return $classes;
}

remove_action( 'customize_register', 'et_divi_customize_register' );

function divi_customize_register_plus_codedrop_functions( $wp_customize ) {
	$google_fonts = et_get_google_fonts();

	$font_choices = array();
	$font_choices['none'] = 'Default Theme Font';
	foreach ( $google_fonts as $google_font_name => $google_font_properties ) {
		$font_choices[ $google_font_name ] = $google_font_name;
	}

	$wp_customize->remove_section( 'title_tagline' );

	$wp_customize->add_section( 'et_divi_settings' , array(
		'title'		=> __( 'Theme Settings', 'Divi' ),
		'priority'	=> 40,
	) );

	$wp_customize->add_section( 'et_google_fonts' , array(
		'title'		=> __( 'Fonts', 'Divi' ),
		'priority'	=> 50,
	) );

	$wp_customize->add_section( 'et_color_schemes' , array(
		'title'       => __( 'Schemes', 'Divi' ),
		'priority'    => 60,
		'description' => __( 'Note: Color settings set above should be applied to the Default color scheme.', 'Divi' ),
	) );

	$wp_customize->add_setting( 'et_divi[link_color]', array(
		'default'		=> '#2EA3F2',
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage'
	) );

	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'et_divi[link_color]', array(
		'label'		=> __( 'Link Color', 'Divi' ),
		'section'	=> 'colors',
		'settings'	=> 'et_divi[link_color]',
	) ) );

	$wp_customize->add_setting( 'et_divi[font_color]', array(
		'default'		=> '#666666',
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage'
	) );

	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'et_divi[font_color]', array(
		'label'		=> __( 'Main Font Color', 'Divi' ),
		'section'	=> 'colors',
		'settings'	=> 'et_divi[font_color]',
	) ) );

	$wp_customize->add_setting( 'et_divi[accent_color]', array(
		'default'		=> '#2EA3F2',
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage'
	) );

	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'et_divi[accent_color]', array(
		'label'		=> __( 'Accent Color', 'Divi' ),
		'section'	=> 'colors',
		'settings'	=> 'et_divi[accent_color]',
	) ) );

	$wp_customize->add_setting( 'et_divi[footer_bg]', array(
		'default'		=> '#222222',
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage'
	) );

	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'et_divi[footer_bg]', array(
		'label'		=> __( 'Footer Background Color', 'Divi' ),
		'section'	=> 'colors',
		'settings'	=> 'et_divi[footer_bg]',
	) ) );

	$wp_customize->add_setting( 'et_divi[menu_link]', array(
		'default'		=> '#666666',
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage'
	) );

	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'et_divi[menu_link]', array(
		'label'		=> __( 'Menu Links Color', 'Divi' ),
		'section'	=> 'colors',
		'settings'	=> 'et_divi[menu_link]',
	) ) );

	$wp_customize->add_setting( 'et_divi[menu_link_active]', array(
		'default'		=> '#2EA3F2',
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage'
	) );

	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'et_divi[menu_link_active]', array(
		'label'		=> __( 'Active Menu Link Color', 'Divi' ),
		'section'	=> 'colors',
		'settings'	=> 'et_divi[menu_link_active]',
	) ) );

	$wp_customize->add_setting( 'et_divi[boxed_layout]', array(
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage',
	) );

	$wp_customize->add_control( 'et_divi[boxed_layout]', array(
		'label'		=> __( 'Boxed Layout', 'Divi' ),
		'section'	=> 'et_divi_settings',
		'type'      => 'checkbox',
		'priority'  => 10,
	) );

	$wp_customize->add_setting( 'et_divi[cover_background]', array(
		'default'       => 'on',
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage',
	) );

	$wp_customize->add_control( 'et_divi[cover_background]', array(
		'label'		=> __( 'Stretch Background Image', 'Divi' ),
		'section'	=> 'et_divi_settings',
		'type'      => 'checkbox',
		'priority'  => 10,
	) );

	$wp_customize->add_setting( 'et_divi[vertical_nav]', array(
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage',
	) );

	$wp_customize->add_control( 'et_divi[vertical_nav]', array(
		'label'		=> __( 'Vertical Navigation', 'Divi' ),
		'section'	=> 'et_divi_settings',
		'type'      => 'checkbox',
		'priority'  => 20,
	) );

	$wp_customize->add_setting( 'et_divi[show_header_social_icons]', array(
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage',
	) );

	$wp_customize->add_control( 'et_divi[show_header_social_icons]', array(
		'label'		=> __( 'Show Social Icons in Header', 'Divi' ),
		'section'	=> 'et_divi_settings',
		'type'      => 'checkbox',
		'priority'  => 30,
	) );

	$wp_customize->add_setting( 'et_divi[show_footer_social_icons]', array(
		'default'       => 'on',
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage',
	) );

	$wp_customize->add_control( 'et_divi[show_footer_social_icons]', array(
		'label'		=> __( 'Show Social Icons in Footer', 'Divi' ),
		'section'	=> 'et_divi_settings',
		'type'      => 'checkbox',
		'priority'  => 40,
	) );

	$wp_customize->add_setting( 'et_divi[show_search_icon]', array(
		'default'       => 'on',
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage',
	) );

	$wp_customize->add_control( 'et_divi[show_search_icon]', array(
		'label'		=> __( 'Show Search Icon', 'Divi' ),
		'section'	=> 'et_divi_settings',
		'type'      => 'checkbox',
		'priority'  => 50,
	) );

	$wp_customize->add_setting( 'et_divi[header_style]', array(
		'default'       => 'left',
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage',
	) );

	$wp_customize->add_control( 'et_divi[header_style]', array(
		'label'		=> __( 'Header Style', 'Divi' ),
		'section'	=> 'et_divi_settings',
		'type'      => 'select',
		'choices'	=> array(
			'left'     => __( 'Default', 'Divi' ),
			'centered' => __( 'Centered', 'Divi' ),
		),
		'priority'  => 55,
	) );
	
	$wp_customize->add_setting( 'et_divi[fixed_header_height]', array(
		'default'       => 'on',
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage',
	) );

	$wp_customize->add_control( 'et_divi[fixed_header_height]', array(
		'label'		=> __( 'Fixed Header Height', 'Divi' ),
		'section'	=> 'et_divi_settings',
		'type'      => 'checkbox',
		'priority'  => 56,
	) );
	
	$wp_customize->add_setting( 'et_divi[no_padding_top_container]', array(
		'default'       => '',
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage',
	) );

	$wp_customize->add_control( 'et_divi[no_padding_top_container]', array(
		'label'		=> __( 'No Padding on Container', 'Divi' ),
		'section'	=> 'et_divi_settings',
		'type'      => 'checkbox',
		'priority'  => 58,
	) );

	
	$wp_customize->add_setting( 'et_divi[transparent_header_height]', array(
		'default'       => 'on',
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage',
	) );

	$wp_customize->add_control( 'et_divi[transparent_header_height]', array(
		'label'		=> __( 'Transparent Header Background (until after first section)', 'Divi' ),
		'section'	=> 'et_divi_settings',
		'type'      => 'checkbox',
		'priority'  => 56,
	) );

	$wp_customize->add_setting( 'et_divi[phone_number]', array(
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage',
	) );

	$wp_customize->add_control( 'et_divi[phone_number]', array(
		'label'		=> __( 'Phone Number', 'Divi' ),
		'section'	=> 'et_divi_settings',
		'type'      => 'text',
		'priority'  => 60,
	) );

	$wp_customize->add_setting( 'et_divi[header_email]', array(
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage',
	) );

	$wp_customize->add_control( 'et_divi[header_email]', array(
		'label'		=> __( 'Email', 'Divi' ),
		'section'	=> 'et_divi_settings',
		'type'      => 'text',
		'priority'  => 70,
	) );

	$wp_customize->add_setting( 'et_divi[primary_nav_bg]', array(
		'default'		=> '#ffffff',
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage'
	) );

	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'et_divi[primary_nav_bg]', array(
		'label'		=> __( 'Primary Navigation Background', 'Divi' ),
		'section'	=> 'colors',
		'settings'	=> 'et_divi[primary_nav_bg]',
		'priority'  => 80,
	) ) );

	$wp_customize->add_setting( 'et_divi[primary_nav_text_color]', array(
		'default'       => 'dark',
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage',
	) );

	$wp_customize->add_control( 'et_divi[primary_nav_text_color]', array(
		'label'		=> __( 'Primary Navigation Text Color', 'Divi' ),
		'section'	=> 'et_divi_settings',
		'type'      => 'select',
		'choices'	=> array(
			'dark'  => __( 'Dark', 'Divi' ),
			'light' => __( 'Light', 'Divi' ),
		),
		'priority'  => 90,
	) );

	$wp_customize->add_setting( 'et_divi[secondary_nav_bg]', array(
		'default'		=> et_get_option( 'accent_color', '#2EA3F2' ),
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage'
	) );

	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'et_divi[secondary_nav_bg]', array(
		'label'		=> __( 'Secondary Navigation Background', 'Divi' ),
		'section'	=> 'colors',
		'settings'	=> 'et_divi[secondary_nav_bg]',
		'priority'  => 100,
	) ) );

	$wp_customize->add_setting( 'et_divi[secondary_nav_text_color]', array(
		'default'       => 'light',
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage',
	) );

	$wp_customize->add_control( 'et_divi[secondary_nav_text_color]', array(
		'label'		=> __( 'Secondary Navigation Text Color', 'Divi' ),
		'section'	=> 'et_divi_settings',
		'type'      => 'select',
		'choices'	=> array(
			'dark'  => __( 'Dark', 'Divi' ),
			'light' => __( 'Light', 'Divi' ),
		),
		'priority'  => 110,
	) );

	$wp_customize->add_setting( 'et_divi[heading_font]', array(
		'default'		=> 'none',
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage'
	) );

	$wp_customize->add_control( 'et_divi[heading_font]', array(
		'label'		=> __( 'Header Font', 'Divi' ),
		'section'	=> 'et_google_fonts',
		'settings'	=> 'et_divi[heading_font]',
		'type'		=> 'select',
		'choices'	=> $font_choices
	) );

	$wp_customize->add_setting( 'et_divi[body_font]', array(
		'default'		=> 'none',
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage'
	) );

	$wp_customize->add_control( 'et_divi[body_font]', array(
		'label'		=> __( 'Body Font', 'Divi' ),
		'section'	=> 'et_google_fonts',
		'settings'	=> 'et_divi[body_font]',
		'type'		=> 'select',
		'choices'	=> $font_choices
	) );
	
	$wp_customize->add_setting( 'et_divi[custom_heading_font]', array(
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage',
	) );

	$wp_customize->add_control( 'et_divi[custom_heading_font]', array(
		'label'		=> __( 'Custom Header Font (Font family)', 'Divi' ),
		'section'	=> 'et_google_fonts',
		'settings'	=> 'et_divi[custom_heading_font]',
		'type'		=> 'text'
	) );
	
	$wp_customize->add_setting( 'et_divi[custom_body_font]', array(
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage',
	) );

	$wp_customize->add_control( 'et_divi[custom_body_font]', array(
		'label'		=> __( 'Custom Body Font (Font family)', 'Divi' ),
		'section'	=> 'et_google_fonts',
		'settings'	=> 'et_divi[custom_body_font]',
		'type'		=> 'text'
	) );

	$wp_customize->add_setting( 'et_divi[color_schemes]', array(
		'default'		=> '',
		'type'			=> 'option',
		'capability'	=> 'edit_theme_options',
		'transport'		=> 'postMessage'
	) );

	$wp_customize->add_control( 'et_divi[color_schemes]', array(
		'label'		=> __( 'Color Schemes', 'Divi' ),
		'section'	=> 'et_color_schemes',
		'settings'	=> 'et_divi[color_schemes]',
		'type'		=> 'select',
		'choices'	=> array(
			'none'   => __( 'Default', 'Divi' ),
			'green'  => __( 'Green', 'Divi' ),
			'orange' => __( 'Orange', 'Divi' ),
			'pink'   => __( 'Pink', 'Divi' ),
			'red'    => __( 'Red', 'Divi' ),
		),
	) );
}
add_action( 'customize_register', 'divi_customize_register_plus_codedrop_functions' );

remove_action( 'wp_head', 'et_divi_add_customizer_css' );

function codedrop_divi_add_customizer_css(){ ?>
	<style>
		a { color: <?php echo esc_html( et_get_option( 'link_color', '#2EA3F2' ) ); ?>; }

		body { color: <?php echo esc_html( et_get_option( 'font_color', '#666666' ) ); ?>; }

		.et_pb_counter_amount, .et_pb_featured_table .et_pb_pricing_heading, .et_quote_content, .et_link_content, .et_audio_content { background-color: <?php echo esc_html( et_get_option( 'accent_color', '#2EA3F2' ) ); ?>; }
		<?php if(et_get_option( 'primary_nav_bg', true ) && !et_get_option('transparent_header_height') ) { ?>
		#main-header, #main-header .nav li ul, .et-search-form, #main-header .et_mobile_menu { background-color: <?php echo esc_html( et_get_option( 'primary_nav_bg', '#ffffff' ) ); ?>; }
		<?php } ?>

		#top-header, #et-secondary-nav li ul { background-color: <?php echo esc_html( et_get_option( 'secondary_nav_bg', et_get_option( 'accent_color', '#2EA3F2' ) ) ); ?>; }
		<?php if(true === et_get_option( 'fixed_header_height', false )) { ?>
			#main-header { padding: 0; }
			#main-header nav#top-menu-nav { padding: 0; }
			#top-menu li { padding: 0; }
			#top-menu li > a { padding: 19px 11px; -moz-transition: all 0.2s ease-in-out; -webkit-transition: all 0.2s ease-in-out; transition: all 0.2s ease-in-out; }
			.nav li ul { top: 100% !important; }
			.et-fixed-header { padding: 0 !important; }
			#main-header.et-fixed-header nav#top-menu-nav { padding: 0; }
			.et-fixed-header #top-menu > li > a { padding: 19px 11px; -moz-transition: all 0.2s ease-in-out; -webkit-transition: all 0.2s ease-in-out; transition: all 0.2s ease-in-out; }
			.et_fixed_nav #logo { padding: 10px 0; max-height: 63px; }
			.et_fixed_nav .et-fixed-header #logo { padding: 10px 0; }
			#et_top_search { margin: 0; padding: 30px 10px; -moz-transition: all 0.2s ease-in-out; -webkit-transition: all 0.2s ease-in-out; transition: all 0.2s ease-in-out; }
			#et_search_icon:before { -moz-transition: all 0.2s ease-in-out; -webkit-transition: all 0.2s ease-in-out; transition: all 0.2s ease-in-out; position: relative; top: 1px; }
			.et-search-form { top: 100% !important; }
			.et-fixed-header #et_top_search { margin: 0; padding: 15px 11px; }
			.et-fixed-header .et-search-form { top: 25px; }
			.et-search-form { top: 47px; }
			.mobile_menu_bar { display: block; }
			a.mobile_nav { display: block; padding: 13px 15px; }
			.et-fixed-header a.mobile_nav { padding: 13px 15px }
			.mobile_menu_bar:before { top: 3px; }
		<?php } ?>
		
		<?php if(true === et_get_option('no_padding_top_container')) { ?>
			.home #page-container { top: 0 !important; padding-top: 0 !important; }
		<?php } ?>
		
		<?php if(true === et_get_option('transparent_header_height', false)) { ?>
			
			#main-header, #main-header .nav li ul, .et-search-form, #main-header .et_mobile_menu { background-color: transparent; box-shadow: none !important; }
			#main-header.main-header-bg, #main-header.main-header-bg .nav li ul, .main-header-bg .et-search-form, #main-header.main-header-bg .et_mobile_menu { background-color: <?php echo esc_html( et_get_option( 'primary_nav_bg', '#ffffff' ) ); ?> !important; }
			
		<?php } ?>

		.woocommerce a.button.alt, .woocommerce-page a.button.alt, .woocommerce button.button.alt, .woocommerce-page button.button.alt, .woocommerce input.button.alt, .woocommerce-page input.button.alt, .woocommerce #respond input#submit.alt, .woocommerce-page #respond input#submit.alt, .woocommerce #content input.button.alt, .woocommerce-page #content input.button.alt, .woocommerce a.button, .woocommerce-page a.button, .woocommerce button.button, .woocommerce-page button.button, .woocommerce input.button, .woocommerce-page input.button, .woocommerce #respond input#submit, .woocommerce-page #respond input#submit, .woocommerce #content input.button, .woocommerce-page #content input.button, .woocommerce-message, .woocommerce-error, .woocommerce-info { background: <?php echo esc_html( et_get_option( 'accent_color', '#2EA3F2' ) ); ?> !important; }

		#et_search_icon:hover, .mobile_menu_bar:before, .footer-widget h4, .et-social-icon a:hover, .comment-reply-link, .form-submit input, .et_pb_sum, .et_pb_pricing li a, .et_pb_pricing_table_button, .et_overlay:before, .entry-summary p.price ins, .woocommerce div.product span.price, .woocommerce-page div.product span.price, .woocommerce #content div.product span.price, .woocommerce-page #content div.product span.price, .woocommerce div.product p.price, .woocommerce-page div.product p.price, .woocommerce #content div.product p.price, .woocommerce-page #content div.product p.price, .et_pb_member_social_links a:hover { color: <?php echo esc_html( et_get_option( 'accent_color', '#2EA3F2' ) ); ?> !important; }

		.woocommerce .star-rating span:before, .woocommerce-page .star-rating span:before, .et_pb_widget li a:hover, .et_pb_bg_layout_light .et_pb_promo_button, .et_pb_bg_layout_light .et_pb_more_button, .et_pb_filterable_portfolio .et_pb_portfolio_filters li a.active, .et_pb_filterable_portfolio .et_pb_portofolio_pagination ul li a.active, .et_pb_gallery .et_pb_gallery_pagination ul li a.active, .wp-pagenavi span.current, .wp-pagenavi a:hover, .et_pb_contact_submit, .et_pb_bg_layout_light .et_pb_newsletter_button, .nav-single a, .posted_in a { color: <?php echo esc_html( et_get_option( 'accent_color', '#2EA3F2' ) ); ?> !important; }

		.et-search-form, .nav li ul, .et_mobile_menu, .footer-widget li:before, .et_pb_pricing li:before, blockquote { border-color: <?php echo esc_html( et_get_option( 'accent_color', '#2EA3F2' ) ); ?>; }

		#main-footer { background-color: <?php echo esc_html( et_get_option( 'footer_bg', '#222222' ) ); ?>; }

		#top-menu a { color: <?php echo esc_html( et_get_option( 'menu_link', '#666666' ) ); ?>; }

		#top-menu li.current-menu-ancestor > a, #top-menu li.current-menu-item > a, .bottom-nav li.current-menu-item > a { color: <?php echo esc_html( et_get_option( 'menu_link_active', '#2EA3F2' ) ); ?>; }

	<?php
		$et_gf_heading_font = sanitize_text_field( et_get_option( 'heading_font', 'none' ) );
		$et_gf_body_font = sanitize_text_field( et_get_option( 'body_font', 'none' ) );
		$custom_heading_font = et_get_option( 'custom_heading_font');
		$custom_body_font = et_get_option( 'custom_body_font');

		if ( 'none' != $et_gf_heading_font || 'none' != $et_gf_body_font && !$custom_heading_font || !$custom_body_font  ) {

			if ( 'none' != $et_gf_heading_font && !$custom_heading_font )
				et_gf_attach_font( $et_gf_heading_font, 'h1, h2, h3, h4, h5, h6' );

			if ( 'none' != $et_gf_body_font && !$custom_heading_font)
				et_gf_attach_font( $et_gf_body_font, 'body, input, textarea, select' );

		}
		
		if($custom_heading_font != '' || $custom_body_font != ''){
			
			if ( $custom_heading_font != '' ){
				echo 'h1, h2, h3, h4, h5, h6 { font-family: '.$custom_heading_font.' !important;}';
			}

			if ( $custom_body_font != '') {
				echo 'body, input, textarea, select { font-family: '.$custom_body_font.' !important;}';
			}
				
			
		}
	?>
	</style>
	
	<?php if(true === et_get_option('transparent_header_height', false)) { ?>
			
		<script type="text/javascript">
			
			jQuery(document).ready(function($){

				$(window).scroll(function(){
				    var fromTopPx = $(window).height() - ($('#main-header').height() * 2); // distance to trigger
				    var scrolledFromtop = $(window).scrollTop();
				    if(scrolledFromtop > fromTopPx){
				        $('#main-header').addClass('main-header-bg');
				    }else{
				        $('#main-header').removeClass('main-header-bg');
				    }
				});
			    
			});
			
		</script>
		
	<?php } ?>
	
<?php }
add_action( 'wp_head', 'codedrop_divi_add_customizer_css', 11 );

function autov_add_loginout_navitem($items) {
	$login_item = '<li class="login">'.wp_loginout($_SERVER['REQUEST_URI'], false).'</li>';
    $items .= $login_item;
	return $items;
}
add_filter('wp_nav_menu_items', 'autov_add_loginout_navitem');

function redirect_to_front_page() {
	global $redirect_to;
	$current_user_id = get_current_user_id();			
	$user_info = get_userdata($current_user_id);
	if($user_info->roles){
		$current_user_role = implode(', ', $user_info->roles) ;
	}
	if ( is_user_logged_in() && $current_user_role !='administrator') {
		$redirect_to = get_option('siteurl');
	}
}
add_action('login_form', 'redirect_to_front_page');

 if( !defined("WPB_VC_VERSION") ){
	define("WPB_VC_VERSION",'4.3.3');	
}
/* 
 * Localization
 */ 
 

// Add save percent next to sale item prices.
//add_filter( 'woocommerce_sale_price_html', 'woocommerce_custom_sales_price', 10, 2 );
//function woocommerce_custom_sales_price( $price, $product ) {
//    $percentage = round( ( ( $product->regular_price - $product->sale_price ) / $product->regular_price ) * 100 );
//    return $price . sprintf( __(' Save %s', 'woocommerce' ), $percentage . '%' );
//}

/** Author: ABU TAHER, Logic-coder IT
 * wp_product_interest_insert 
 *
 *  @param $wp_product_interest_data, $format_array
 */
function wp_product_interest_insert( $wp_product_interest_data, $format_array  ){
	//print_r( $wp_product_interest_data ); exit; 
	global $wpdb;
	$wpdb->insert( 'wp_product_interest', $wp_product_interest_data , $format_array=null );
	return $wpdb->insert_id;
}
/** Author: ABU TAHER, Logic-coder IT
 * wp_product_interest_update 
 *
 *  @param $wp_product_interest_data, $format_array
 */
function wp_product_interest_update( $wp_product_interest_data, $format_array, $product_interest_id ){
	global $wpdb;
	$where = array( "product_interest_id"=>$product_interest_id );
	$where_format = array();  
	return $wpdb->update( 'wp_product_interest', $wp_product_interest_data, $where, $format_array = null, $where_format = null );
	//$wpdb->insert( 'wp_product_interest', $wp_product_interest_data , $format_array );
	//return $wpdb->insert_id;
}
/** Author: ABU TAHER, Logic-coder IT
 * wp_product_interest_meta_insert 
 *
 *  @param $product_interest_insert_id, $wp_product_interest_data, $product_attributes
 */
function wp_product_interest_meta_insert( $product_interest_insert_id, $wp_product_interest_data, $product_attributes ){
	global $wpdb;
	$wp_product_interest_meta_data = array( 'product_interest_id' => $product_interest_insert_id, 
																	'user_id'=>$wp_product_interest_data['user_id'], 
																	'product_id'=> $wp_product_interest_data['product_id'] );
	$format_array = array('%d', '%s', '%d');
	if( $product_attributes ) {
		foreach($product_attributes as $product_attribute) {
			$wp_product_interest_meta_data['meta_name'] = $product_attribute['name'];
			$wp_product_interest_meta_data['meta_value'] = $product_attribute['value'];
			$wp_product_interest_meta_data['add_date'] = date("Y-m-d");
			$format_array = array('%s', '%s', '%s');
			$wpdb->insert( 'wp_product_interest_meta', $wp_product_interest_meta_data , $format_array ); 
		}
	}			
	return $wpdb->insert_id;
}
/** Author: ABU TAHER, Logic-coder IT
 * wp_product_interest_meta_update
 *
 * @param $product_interest_id, $wp_product_interest_data, $product_attributes
 */
function wp_product_interest_meta_update( $product_interest_id, $wp_product_interest_data, $product_attributes ){ 
	global $wpdb;
	$where = array( 'product_interest_id' => $product_interest_id ); 
	$where_format = array();
	$success = $wpdb->delete( 'wp_product_interest_meta', $where, $where_format = null );
	//if( $success ){
		$wp_product_interest_meta_data = array( 'product_interest_id' => $product_interest_id, 
																	'user_id'=>$wp_product_interest_data['user_id'], 
																	'product_id'=> $wp_product_interest_data['product_id'] );
		$format_array = array('%d', '%s', '%d');
		if( $product_attributes ) {
			foreach($product_attributes as $product_attribute) {
				$wp_product_interest_meta_data['meta_name'] = $product_attribute['name'];
				$wp_product_interest_meta_data['meta_value'] = $product_attribute['value'];
				$wp_product_interest_meta_data['add_date'] = date("Y-m-d");
				$format_array = array('%s', '%s', '%s');
				$wpdb->insert( 'wp_product_interest_meta', $wp_product_interest_meta_data , $format_array ); 
			}
		}			
		return $wpdb->insert_id;
	//}	
}
/** Author: ABU TAHER, Logic-coder IT
 * wp_product_interest_assign 
 *
 *  @param $interest_assign_data, $interest_id, $product_id, $user_id
* return Success/Failure Message 
 */
function wp_product_interest_assign( $interest_assign_data, $interest_id, $product_id, $user_id ){
	global $wpdb;
	$wp_product_interest_meta_data = array( 'product_interest_id' => $interest_id, 
																	'user_id'=>$user_id, 
																	'product_id'=> $product_id );
	$format_array = array('%d', '%s', '%d');
		if( $interest_assign_data ) {
			foreach($interest_assign_data as $product_attribute) {
				$wp_product_interest_meta_data['meta_name'] = $product_attribute['name'];
				$wp_product_interest_meta_data['meta_value'] = $product_attribute['value'];
				$wp_product_interest_meta_data['add_date'] = date("Y-m-d");
				$format_array = array('%s', '%s', '%s');
				$wpdb->insert( 'wp_product_interest_meta', $wp_product_interest_meta_data , $format_array ); 
			}
		}			
	return $wpdb->insert_id;
}
/** Author: ABU TAHER, Logic-coder IT
 * Ajax function
 *
 */


/////////////////////////// Admin Ajax ////////////////////////////

add_action( 'wp_ajax_update_interest_unit_price', 'update_interest_unit_price_callback' );
function update_interest_unit_price_callback() {
	 global $wpdb; // this is how you get access to the database
     $product_interest_id = $_POST['product_interest_id'];
     $unit_price= $_POST['unit_price'];
	 $where = array( "product_interest_id" => $product_interest_id); 
	 $price_data = array( "interest_unit_price"=> $unit_price );  
	 $format_array = array('%f');		
	 echo $wpdb->update( 'wp_product_interest', $price_data, $where, $format_array = null, $where_format = null );		
     exit(); 	  
}
add_action( 'wp_ajax_send_email_to_interester', 'send_email_to_interester_callback' );
function send_email_to_interester_callback() {
	 global $wpdb, $current_user;
	 $current_user = wp_get_current_user();	 
	 $dear_text =""; 
	 $interest_start_date = "";
	 $interest_end_date = "";
     $product_interest_id = $_POST['product_interest_id'];    
     $email_message_text = $_POST['email_message_text'];    
	 $results_interest = $wpdb->get_results( " SELECT * FROM wp_users, wp_product_interest, wp_posts WHERE wp_users.ID = wp_product_interest.user_id AND wp_product_interest.product_interest_id='".$product_interest_id."' AND wp_posts.ID=wp_product_interest.product_id" );	
	if( $results_interest ){
			$user_meta_info = get_user_meta( $results_interest[0]->user_id, "" , "" ); 
			//return (print_r( $user_meta_info )); exit;
			if( $user_meta_info['first_name'][0] ){ 
				$dear_text = $user_meta_info['first_name'][0];
			}else{
				$dear_text = $results_interest[0]->display_name; 
		}
		if( $results_interest[0]->interest_start_date ){
			$interest_start_date = date("Y-m-d", $results_interest[0]->interest_start_date );
			$interest_end_date = date("Y-m-d", $results_interest[0]->interest_end_date );
		}else{ $interest_start_date = __("As soon as price is reasonable"); }
			//////////////////////////////////////////////
			
			//$email_to = "tahersumonabu@gmail.com";
			$email_to = $results_interest[0]->user_email;
			/******************************************/
			$subject="!NMID: A Business Aggregator\n\n";
			$message  = "<html><body>"."\n";
			$message .="<table cellpadding='0' cellspacing='0' bgcolor=#319d00 width='100%' style='margin:0 auto'><tr style='font-family: Verdana,Arial,Helvetica,sans-serif; font-size: 11px; color: rgb(255,255,255); line-height: 140%;'><td width='23'></td><td><span>!NMID: A Business Aggregator</span></td></tr></table>"."\n\n";
			$message .="<p>Dear&nbsp;".$dear_text.",</p>"."\n"; 
			$message .="<p>".$email_message_text."</p>"."\n";
			$message .="<p>Your Interest Details:</p>"."\n";
			$message .="<p><b>Product Name: </b><a href=".get_site_url()."/my-interest-list/?action=edit&product_interest_id=".$results_interest[0]->product_interest_id."&product_name=".$results_interest[0]->post_name." >".$results_interest[0]->product_name."</a></p>\n";	
			$message .="<p><b>Qty: </b>".$results_interest[0]->interest_qty."</p>\n";	
			$message .="<p><b>Interest Start Date: </b>".$interest_start_date."</p>\n";	
			$message .="<p><b>Interest End Date: </b>".$interest_end_date."</p>\n";	
				
			$message .="<table cellpadding='0' cellspacing='0' bgcolor=#319d00 width='100%' style='margin:0 auto'><tr style='font-family: Verdana,Arial,Helvetica,sans-serif; font-size: 11px; color: rgb(255,255,255); line-height: 140%;'><td width='23'></td><td><span>!NMID: A Business Aggregator</span></td></tr></table>"."\n\n";
			$message .= "</body></html>\n";
			$uid = md5(uniqid(time()));
			$header  = "From: !NMID <".$current_user->user_email.">\r\n";
			$header .= "Reply-To:".$current_user->user_email."\r\n";
			$header .= "MIME-Version: 1.0\r\n";
			$header .= "Content-Type: multipart/mixed; boundary=\"".$uid."\"\r\n\r\n";
			$header .= "This is a multi-part message in MIME format.\r\n";
			$header .= "--".$uid."\r\n";
			$header .= "Content-type:text/html; charset=iso-8859-1\r\n";
			$header .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
			$header .= $message."\r\n\r\n";
			$header .= "--".$uid."\r\n";
			//$header .= "Content-Type: application/octet-stream; name=\"".$attachment_name."\"\r\n"; // use different content types here
			$header .= "Content-Transfer-Encoding: base64\r\n";
			//$header .= "Content-Disposition: attachment; filename=\"".$attachment_name."\"\r\n\r\n";
			//$header .= $attachedfile."\r\n\r\n";
			//echo $message; exit;
			$header .= "--".$uid."--";			
			$attachments =""; 
			$messages = ""; 					
			if( mail( $email_to , $subject,"",$header) )	{   
				return True;  
			}
			else{
				return False;   
			}
		}
}

/////////////////////////// Admin Ajax ////////////////////////////
function quick_view_inmid_interest_ajax_function(){    	
	 wp_localize_script( 'function', 'quick_view_inmid_interest_ajax_script', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
	 wp_enqueue_script( 'function', CODEDROP_DIR .'/init/js/quick_view_inmid_interest_ajax.js', 'jquery', true);
}
add_action('template_redirect', 'quick_view_inmid_interest_ajax_function');
add_action("wp_ajax_nopriv_save_my_interest", "save_my_interest_callback");
add_action("wp_ajax_save_my_interest", "save_my_interest_callback");

function save_my_interest_callback(){ 	
	//echo get_share_buttons();
	/*if ( !is_user_logged_in() ) { 
		wp_redirect( get_site_url()."/login", 301 ); 
	}*/
	$current_user_id = get_current_user_id();			
	$user_info = get_userdata($current_user_id);
	$all_meta_for_user = get_user_meta( $current_user_id );
		
	if($user_info->roles){
		$current_user_role = implode(', ', $user_info->roles) ;
	}
	$user_price_lists = get_field('shopping_lists', 'user_'.$current_user_id);			
	$field = get_field_object('shopping_lists', 'user_'.$current_user_id);				
	// Common Field For all types of user
	$product_id     = $_POST['product_id']; 
	$interest_data = $_POST['interest_data']; 	
	$product_name = $_POST['prod_name']; 
	$product_attributes = $_POST['product_attributes_data']; 
	//print_r( $product_attributes ); exit;
	
	$asa_price_is_reasonable = ( $interest_data['asa_price_is_reasonable'] ? 1 : "" );
	$interest_start_date = ( $interest_data['interest_start_date'] ? $interest_data['interest_start_date'] : "" );
	$interest_date_range = ( $interest_data['interest_date_range'] ? $interest_data['interest_date_range'] : "" );
	$interest_qty = ( $interest_data['interest_qty'] ? $interest_data['interest_qty'] : "" );
	$interest_notes = ( $interest_data['interest_notes'] ? $interest_data['interest_notes'] : "" );	
	$interest_recuring_purchase = ( $interest_data['interest_recuring_purchase'] ? $interest_data['interest_recuring_purchase'] : "" );		
	$interest_visitor_email = "";
	$interest_visitor_phone = "";
	$interest_authorative_person = "";
	$authorative_person_first_name = "";
	$authorative_person_last_name = "";
	$authorative_person_email = "";
	$authorative_person_phone = "";
	$product_interest_meta_insert_id = "";
	// Form validation For all types of user
	$interest_validation_errors = new WP_Error(); 
	// Validate interest time duration
	$asa_price_is_reasonable = apply_filters( 'pre_interest_asa_price_is_reasonable', $asa_price_is_reasonable);
	
	$interest_start_date = apply_filters( 'pre_interest_start_date', $interest_start_date);
	if( empty($asa_price_is_reasonable) && empty($interest_start_date)){
		$interest_validation_errors->add('empty_interest_time_duration', __('Please choose interest time duration!!!') );
	}
	if( !empty($asa_price_is_reasonable) && !empty($interest_start_date)){
		$interest_validation_errors->add('empty_interest_time_duration', __('Please choose only one interest time duration!!!') );
	}
	if( empty( $asa_price_is_reasonable ) && !empty( $interest_start_date ) ){
			$interest_start_date_arr = explode("-",$interest_start_date);
			$interest_start_date_time_as_text = mktime( 0, 0, 0, ($interest_start_date_arr[1] ), $interest_start_date_arr[2], $interest_start_date_arr[0] );
			$interest_start_date_deafult = date( 'Y-m-d', strtotime( date('Y-m-d'). ' + 14 days' ) );
			$interest_start_date_deafult_arr = explode( "-",$interest_start_date_deafult );
			$interest_start_date_deafult_text = mktime( 0, 0, 0, ( $interest_start_date_deafult_arr[1] ), $interest_start_date_deafult_arr[2], $interest_start_date_deafult_arr[0] );
			if( $interest_start_date_time_as_text < $interest_start_date_deafult_text ){
				$interest_validation_errors->add( 'empty_interest_time_duration', __( 'Interest starting date should be '.$interest_start_date_deafult. ' Or higher!!!' ) );	
			}   
		}
		// Validate interest_date_range
		if( empty( $asa_price_is_reasonable ) ){
			$interest_date_range = apply_filters( 'pre_interest_date_range', $interest_date_range );
			if( empty( $interest_date_range ) ){	
				$interest_validation_errors->add( 'empty_interest_date_range', __( 'Interest To should not be empty!!!' ) );	
			}
		}
		// Validate interest Qty
		$interest_qty= apply_filters( 'pre_interest_qty', $interest_qty ); 
		if( empty( $interest_qty ) ){	
			$interest_validation_errors->add('empty_interest_qty', __('Interest Quantity should not be empty!!!') );	
		}elseif(!ctype_digit($interest_qty)){
			$interest_validation_errors->add('invalid_interest_qty', __('Interest Quantity should be a number!!!') );	
		}
		// Form validation >> Visitor 
		if ( !is_user_logged_in() ) { 
			$interest_visitor_email = stripslashes_deep( $interest_data['interest_visitor_email']);
			$interest_visitor_phone = stripslashes_deep( $interest_data['interest_visitor_phone']);
			// Validate E-mail
			$interest_visitor_email = apply_filters( 'pre_interest_visitor_email', $interest_visitor_email);
			if ( empty( $interest_visitor_email ) ) {
				$interest_validation_errors->add('empty_interest_visitor_email', __('E-mail can not be empty!!!') );
			}
			if ( !empty( $interest_visitor_email ) && !is_email( $interest_visitor_email ) ) {
				$interest_validation_errors->add('invalid_interest_visitor_email', __('Invalid E-mail, please choose another one!!!') );
			}
			if ( ! defined( 'WP_IMPORTING' ) && email_exists( $interest_visitor_email ) ) {
				$interest_validation_errors->add( 'existing_interest_visitor_email', __( 'Sorry, that email address is already used!!!' ) );
			}			
			// Validate Phone
			$interest_visitor_phone = apply_filters( 'pre_interest_visitor_phone', $interest_visitor_phone ); 
			if ( empty( $interest_visitor_phone ) ) {
				$interest_validation_errors->add('empty_interest_visitor_phone', __('Phone can not be empty!!!') );
			}elseif( !preg_match("/[^0-9]/", $interest_visitor_phone) && strlen($interest_visitor_phone)!=10){
			//$interest_validation_errors->add('invalid_interest_visitor_phone', __('Please enter valid phone number!!!') );
			}
		}
		//Form Validation >> User role company
		if($current_user_role=="company"){
			$interest_authorative_person = stripslashes_deep( $interest_data['interest_authorative_person']); 
			// Validate authorative person
			$interest_authorative_person = apply_filters( 'pre_interest_authorative_person', $interest_authorative_person);
			if(empty( $interest_authorative_person ) ){
				$interest_validation_errors->add('empty_interest_authorative_person', __('Please choose Authoritative person for this purchase interest!!!') );
			}elseif($interest_authorative_person=="authorative_person_one"){
				$authorative_person_first_name= $all_meta_for_user['authorative_person_one_first_name'][0];
				$authorative_person_last_name = $all_meta_for_user['authorative_person_one_last_name'][0];
				$authorative_person_email = $all_meta_for_user['authorative_person_one_email'][0];
				$authorative_person_phone = $all_meta_for_user['authorative_person_one_phone'][0];
				if(empty($all_meta_for_user['authorative_person_one_first_name'][0])){
					$interest_validation_errors->add('empty_authorative_person_one_first_name', __('Authoritative person first name can&rsquo;t be empty!!!') );
				}if(empty($all_meta_for_user['authorative_person_one_last_name'][0])){
					$interest_validation_errors->add('empty_authorative_person_one_last_name', __('Authoritative person last name can&rsquo;t be empty!!!') );
				}if(empty($all_meta_for_user['authorative_person_one_phone'][0])){
					$interest_validation_errors->add('empty_authorative_person_one_phone', __('Authoritative person phone can&rsquo;t be empty!!!') );
				}if(empty($all_meta_for_user['authorative_person_one_email'][0])){
					$interest_validation_errors->add('empty_authorative_person_one_email', __('Authoritative person E-mail can&rsquo;t be empty!!!') );
				}							
			}elseif($interest_authorative_person=="authorative_person_two"){
				$authorative_person_first_name = $all_meta_for_user['authorative_person_two_first_name'][0];
				$authorative_person_last_name = $all_meta_for_user['authorative_person_two_last_name'][0];
				$authorative_person_email = $all_meta_for_user['authorative_person_two_email'][0];
				$authorative_person_phone = $all_meta_for_user['authorative_person_two_phone'][0];
				if(empty($all_meta_for_user['authorative_person_two_first_name'][0])){
					$interest_validation_errors->add('empty_authorative_person_two_first_name', __('Authoritative person first name can&rsquo;t be empty!!!') );
				}if(empty($all_meta_for_user['authorative_person_two_last_name'][0])){
					$interest_validation_errors->add('empty_authorative_person_two_last_name', __('Authoritative person last name can&rsquo;t be empty!!!') );
				}if(empty($all_meta_for_user['authorative_person_two_phone'][0])){
					$interest_validation_errors->add('empty_authorative_person_two_phone', __('Authoritative person phone can&rsquo;t be empty!!!') );
				}if(empty($all_meta_for_user['authorative_person_two_email'][0])){
					$interest_validation_errors->add('empty_authorative_person_two_email', __('Authoritative person E-mail can&rsquo;t be empty!!!') );
				}
			}elseif($interest_authorative_person=="authorative_person_three"){
				$authorative_person_first_name = $all_meta_for_user['authorative_person_three_first_name'][0];
				$authorative_person_last_name = $all_meta_for_user['authorative_person_three_last_name'][0];
				$authorative_person_email = $all_meta_for_user['authorative_person_one_email'][0];
				$authorative_person_phone = $all_meta_for_user['authorative_person_three_phone'][0];
				if(empty($all_meta_for_user['authorative_person_three_first_name'][0])){
					$interest_validation_errors->add('empty_authorative_person_three_first_name', __('Authoritative person first name can&rsquo;t be empty!!!') );
				}if(empty($all_meta_for_user['authorative_person_three_last_name'][0])){
					$interest_validation_errors->add('empty_authorative_person_three_last_name', __('Authoritative person last name can&rsquo;t be empty!!!') );
				}if(empty($all_meta_for_user['authorative_person_three_phone'][0])){
					$interest_validation_errors->add('empty_authorative_person_three_phone', __('Authoritative person phone can&rsquo;t be empty!!!') );
				}if(empty($all_meta_for_user['authorative_person_three_email'][0])){
					$interest_validation_errors->add('empty_authorative_person_three_email', __('Authoritative person E-mail can&rsquo;t be empty!!!') );
				}
			}
		//$interest_purchase_eval_start_date = stripslashes_deep($_POST['interest_purchase_eval_start_date']);
		//$interest_purchase_eval_end_date = stripslashes_deep($_POST['interest_purchase_eval_end_date']);
		}				
		// Form validation finish here		
		if ( sizeof( $interest_validation_errors->get_error_messages() ) <= 0 )  { 
		// Prepare Data to insert in wp_product_interest: Table and wp_product_interest_meta:Table
		$wp_product_interest_data['user_id'] = $current_user_id; 
		$wp_product_interest_data['product_id'] = $product_id;
		$wp_product_interest_data['product_name'] = $product_name;
		$format_array = array('%s', '%d', '%s'); 
		if($asa_price_is_reasonable){
			$wp_product_interest_data['asa_price_is_reasonable'] = 1;
			array_push($format_array, '%d');
		}elseif($interest_start_date_time_as_text){
			$interest_end_date = date('Y-m-d', strtotime($interest_start_date. ' + '.$interest_date_range. 'days'));
			$interest_end_date_arr = explode("-",$interest_end_date);
			$interest_end_date_time_as_text = mktime(0, 0, 0, ($interest_end_date_arr[1]), $interest_end_date_arr[2], $interest_end_date_arr[0]);
			$wp_product_interest_data['interest_start_date'] = $interest_start_date_time_as_text;
			$wp_product_interest_data['interest_date_range'] = $interest_date_range;
			$wp_product_interest_data['interest_end_date'] = $interest_end_date_time_as_text;
			array_push( $format_array, '%s', '%s', '%s' );
		}					
		$wp_product_interest_data['interest_qty'] = $interest_qty;
		$wp_product_interest_data['interest_notes'] = $interest_notes;
		$wp_product_interest_data['interest_recuring_purchase'] = $interest_recuring_purchase; 
		array_push($format_array, '%d', '%s', '%s');
		if ( !is_user_logged_in() ) { 
			$wp_product_interest_data['interest_visitor_email'] = $interest_visitor_email;
			$wp_product_interest_data['interest_visitor_phone'] = $interest_visitor_phone;
			array_push($format_array, '%s', '%s');
		}					
		if( $interest_authorative_person ){
			$wp_product_interest_data['authorative_person'] = $interest_authorative_person;
			$wp_product_interest_data['authorative_person_first_name'] = $authorative_person_first_name;
			$wp_product_interest_data['authorative_person_last_name'] = $authorative_person_last_name;
			$wp_product_interest_data['authorative_person_email'] = $authorative_person_email;
			$wp_product_interest_data['authorative_person_phone'] = $authorative_person_phone;
			array_push($format_array, '%s', '%s' , '%s' , '%s', '%s');
		}
		$wp_product_interest_data['add_date'] = date("Y-m-d");
		array_push($format_array, '%s');		
		$product_interest_insert_id = wp_product_interest_insert( $wp_product_interest_data, $format_array );
		if( $product_attributes ) { 
			foreach( $product_attributes as $product_attribute ) {
				if( $interest_data[ $product_attribute['label'] ] ){
					$product_attr[] = array('name' => $product_attribute['label'], 'value' =>  $interest_data[ $product_attribute['label'] ] );							
				}				
				//$product_attr[] = array('name' => $product_attribute['label'], 'value' => $_POST[$product_attribute['label']]);							
			}							
		}		
		if($product_interest_insert_id && sizeof($product_attr) > 0 ){
			$product_interest_meta_insert_id = wp_product_interest_meta_insert( $product_interest_insert_id, $wp_product_interest_data, $product_attr );
		}					
		if( $product_interest_insert_id  > 0 ){
			echo '<div class="notice">Congradulations!!! Product '.$product_name.' was successfully added to your interest list! <span class="close"></span></div>';
		}
	}else{
		$show_this_div = 1; /*display_error_message*/ 
			if ( sizeof( $interest_validation_errors->get_error_messages() ) > 0 )  { 
				echo '<div class="error" style="padding: 5px;"><p>';
				foreach ( $interest_validation_errors->get_error_messages($code) as $error ) {
					echo $error . "<br />\n";   
				}
				echo '</p></div>';  
			}					
		}
		//print_r( $interest_validation_errors ); 
		exit;
    die();
}
/** Author: ABU TAHER, Logic-coder IT
 * wp_my_interest_list 
 *
 *  @param $current_user_id 
 */
function wp_my_interest_list( $current_user_id ){ 
global $wpdb;
$results = $wpdb->get_results( "SELECT * FROM wp_product_interest
					JOIN wp_posts ON wp_product_interest.product_id = 				
					wp_posts.ID WHERE wp_product_interest.user_id='".	
					$current_user_id."' AND ( wp_product_interest.interest_end_date >= '".time()."' OR wp_product_interest.asa_price_is_reasonable=1) ORDER BY wp_product_interest.product_interest_id DESC", OBJECT );
		//print_r($results); exit;
return $results;
}
/** Author: ABU TAHER, Logic-coder IT
 * wp_my_interest_meta
 *
 *  @param $current_user_id, $product_interest_id
 */
function wp_my_interest_meta( $current_user_id, $product_interest_id ){
global $wpdb;
$results = $wpdb->get_results( "SELECT * FROM wp_product_interest
					JOIN wp_product_interest_meta ON wp_product_interest.product_interest_id = 				
					wp_product_interest_meta.product_interest_id WHERE wp_product_interest.user_id='".	
					$current_user_id."' AND wp_product_interest.product_interest_id ='".	
					$product_interest_id."' AND ( wp_product_interest.interest_end_date >= '".time()."' OR wp_product_interest.asa_price_is_reasonable=1)
					ORDER BY wp_product_interest_meta.product_interest_meta_id ASC", OBJECT );
		//print_r($results); exit;
		if( empty( $results ) ){
			$results = $wpdb->get_results( "SELECT * FROM wp_product_interest
					WHERE wp_product_interest.user_id='".$current_user_id."' AND 
					wp_product_interest.product_interest_id ='".	$product_interest_id."' AND ( 
					wp_product_interest.interest_end_date >= '".time()."' OR 
					wp_product_interest.asa_price_is_reasonable=1)", OBJECT );
		}
return $results;
}
/** Author: ABU TAHER, Logic-coder IT
 * wp_check_valid_user_action
 *
 *  @param $current_user_id, $product_interest_id, $check_for
* Return True/False AND wp_product_interest.interest_confirmed=0
 */ 
function wp_check_valid_user_action( $current_user_id, $product_interest_id, $check_for ){
	global $wpdb;	
	if($check_for == "product_interest"){ 
		$results = $wpdb->get_results( "SELECT * FROM wp_product_interest, wp_posts 
						WHERE ( wp_product_interest.product_interest_id ='".$product_interest_id."' AND 	
						wp_product_interest.user_id ='".$current_user_id."' ) AND ( wp_product_interest.product_id= 	
						wp_posts.ID AND wp_posts.post_status='publish') 
						AND ( wp_product_interest.interest_end_date >= '".time()."' 
						OR wp_product_interest.asa_price_is_reasonable=1)" );
	}
	if( sizeof($results) > 0 ){
		return True;
	}else{ return False; }
}
/** Author: ABU TAHER, Logic-coder IT
 * wp_remove_interest
 *
 *  @param $current_user_id, $product_interest_id 
 */ 
function wp_remove_interest( $current_user_id, $product_interest_id ){
	global $wpdb;	
	$delete_rows = $wpdb->query("DELETE FROM wp_product_interest WHERE product_interest_id ='".
	$product_interest_id."' AND user_id ='".$current_user_id."'");
	if( $delete_rows ){
		$delete_meta_rows = $wpdb->query("DELETE FROM wp_product_interest_meta WHERE product_interest_id 
		='".$product_interest_id."' AND user_id ='".$current_user_id."'");
	}
	return $delete_rows;
}
/** Author: ABU TAHER, Logic-coder IT
 * user_product_interest
 *
 * Return interest Lists  
 */ 
function user_product_interest( ){
	global $wpdb, $paged, $max_num_pages, $current_date, $sql_posts_total;
	$paged = ($_GET['paged']) ? $_GET['paged'] : 1;
	$post_per_page = 12;
	$offset = ($paged - 1) * $post_per_page;	
	$sql_result = $wpdb->get_results( "SELECT SQL_CALC_FOUND_ROWS *,  sum(	interest_qty ) as total_qty, wp_posts.ID FROM wp_product_interest
	JOIN wp_posts ON wp_product_interest.product_id = 				
	wp_posts.ID AND wp_product_interest.interest_group_id=0 GROUP BY wp_posts.ID ORDER BY wp_product_interest.interest_end_date ASC  LIMIT $offset,$post_per_page", OBJECT );
	$sql_posts_total = $wpdb->get_var( "SELECT FOUND_ROWS();" ); 
	$max_num_pages = ceil($sql_posts_total / $post_per_page);
	return $sql_result;
}
/** Author: ABU TAHER, Logic-coder IT
 * user_product_interest
 *@param $product_id
 * Return interest Lists  By $product_id
 */
function get_product_interest_lists( $product_id ){
	global $wpdb, $paged, $max_num_pages, $current_date, $sql_posts_total;
	$paged = ($_GET['paged']) ? $_GET['paged'] : 1;
	$post_per_page = 12;
	$offset = ($paged - 1) * $post_per_page;	
	$sql_result = $wpdb->get_results( "SELECT SQL_CALC_FOUND_ROWS * FROM wp_product_interest 
	JOIN wp_posts ON wp_product_interest.product_id = 				
	wp_posts.ID AND wp_product_interest.interest_group_id=0
	WHERE wp_product_interest.product_id='".$product_id."' ORDER BY wp_product_interest.interest_end_date ASC  LIMIT $offset,$post_per_page", OBJECT );
	$sql_posts_total = $wpdb->get_var( "SELECT FOUND_ROWS();" ); 
	$max_num_pages = ceil( $sql_posts_total / $post_per_page );
	return $sql_result;
}
/** Author: ABU TAHER, Logic-coder IT
 * get_product_statics
 *@param $product_id
 * Return product statics By $product_id
 */ 
function get_product_statistics( $product_id ){
	global $wpdb, $sql_interest_total;	
	$sql_result = $wpdb->get_results( "SELECT SQL_CALC_FOUND_ROWS * FROM wp_product_interest WHERE product_id='".$product_id."' AND wp_product_interest.interest_group_id=0", OBJECT );
	$sql_interest_total = $wpdb->get_var( "SELECT FOUND_ROWS();" ); 
	return $sql_result;
}
/** Author: ABU TAHER, Logic-coder IT
 * get_campaign_under_evaluation_by_product, If at least one Interest for this product
 *@param $product_id
 * Return campaing which is under evaluation By $product_id
 */ 
function get_campaign_under_evaluation_by_product( $product_id ){ 
	global $wpdb, $under_evaluation;	
	$sql_result = $wpdb->get_results( "SELECT SQL_CALC_FOUND_ROWS * FROM wp_product_interest WHERE product_id='".$product_id."' AND interest_paid=0 AND interest_campaign_closed=0", OBJECT );
	$under_evaluation = $wpdb->get_var( "SELECT FOUND_ROWS();" ); 	
	return $under_evaluation;
}
/** Author: ABU TAHER, Logic-coder IT
 * get_campaign_mmq_reached_by_product , If sum(	interest_qty ) is greater or equal to minimum_target_sells for this product whatever paid or unpaid
 *@param $product_id
 * Return campaing for which MMQ reached By $product_id
 */ 
function get_campaign_mmq_reached_by_product( $product_id , $product_interest_id , $flag ){ 
	global $wpdb, $total_interest;	//minimum_target_sells
	$minimum_target_sells = get_post_meta( $product_id, "minimum_target_sells", "single" ); 	
	if( $flag == "in_general" ){
			$sql_result = $wpdb->get_results( " SELECT sum(	interest_qty ) as total_qty  FROM wp_product_interest WHERE product_id='".$product_id."' AND interest_campaign_closed=0 ", OBJECT );
	}elseif( $flag=="for_interest_group" ){ 
			$sql_result = $wpdb->get_results( " SELECT SQL_CALC_FOUND_ROWS * ,  sum( interest_qty ) as total_qty FROM wp_product_interest WHERE interest_group_id = ( SELECT interest_group_id from wp_product_interest WHERE product_interest_id='".$product_interest_id."' AND interest_campaign_closed=0 ) ", OBJECT ); 					
	}
	if( $sql_result[0]->total_qty >= $minimum_target_sells ){
		return 1;
	}else{ return 0;}
}
/** Author: ABU TAHER, Logic-coder IT
 * user_product_interest
 *@Param $product_interest_id
 * Return interest Lists  
 */ 
function user_product_interest_details( $product_interest_id ){
	global $wpdb;
	$sql_result = $wpdb->get_results( "SELECT * FROM wp_product_interest
	JOIN wp_posts ON wp_product_interest.product_id = 				
	wp_posts.ID WHERE wp_product_interest.product_interest_id = '".$product_interest_id."'", OBJECT );
	return $sql_result;
}
/** Author: ABU TAHER, Logic-coder IT
 * get_user_product_interest_meta
 *@Param $product_interest_id
 * Return interest Lists  
 */ 
function get_user_product_interest_meta( $product_interest_id ){
	global $wpdb;
	$sql_result = $wpdb->get_results( "SELECT * FROM wp_product_interest_meta	
	JOIN wp_product_interest ON wp_product_interest_meta.product_interest_id = 				
	wp_product_interest.product_interest_id WHERE wp_product_interest_meta.product_interest_id = '".$product_interest_id."'", OBJECT );
	return $sql_result;
}
/** Author: ABU TAHER, Logic-coder IT
 * sum_qty_for_group_interest
 *@Param $product_id, $group_id
 * Return total quantity for this group 
 */ 
function sum_qty_for_group_interest( $product_id , $group_id ){
	//echo $product_id ." >> ". $group_id; exit;
	global $wpdb;
	$sql_result = $wpdb->get_results( " SELECT sum( interest_qty ) as total_qty FROM wp_product_interest  WHERE product_id='".$product_id."' AND interest_group_id='".$group_id."' AND interest_confirmed= 0 AND interest_campaign_closed= 0", OBJECT );
	return $sql_result;
}
/** Author: ABU TAHER, Logic-coder IT
 * count_interest_confirmed
 *@Param $product_id, $group_id
 * Return total quantity for confirmed interest within this group 
 */ 
function count_interest_confirmed( $product_id , $group_id ){ 
	global $wpdb;
	$sql_result = $wpdb->get_results( " SELECT sum( interest_qty ) as total_qty FROM wp_product_interest  WHERE product_id='".$product_id."' AND interest_group_id='".$group_id."' AND interest_confirmed= 1 ", OBJECT );
	//print_r( $sql_result ); 
	return $sql_result;
}
/** Author: ABU TAHER, Logic-coder IT
 * count_interest_failed
 *@Param $product_id, $group_id
 * Return total quantity for failed interest within this group 
 */ 
function count_interest_failed( $product_id , $group_id ){ 
	global $wpdb;
	$sql_result = $wpdb->get_results( " SELECT sum( interest_qty ) as total_qty FROM wp_product_interest  WHERE product_id='".$product_id."' AND interest_group_id='".$group_id."' AND interest_campaign_closed= 2 ", OBJECT );
	//print_r( $sql_result ); 
	return $sql_result;
}
/** Author: ABU TAHER, Logic-coder IT
 * sum_qty_for_product_interest
 *@Param $product_id,  $flag= "not_in_group", $product_interest_id=""
 * Return total quantity By  $product_id
 */ 
function sum_qty_for_product_interest( $product_id ,$product_interest_id, $flag ){
	global $wpdb;
	if( $flag=="not_in_group" ){
			$sql_result = $wpdb->get_results( " SELECT sum( interest_qty ) as total_qty FROM wp_product_interest  WHERE product_id='".$product_id."' AND interest_group_id=0", OBJECT );
	}elseif( $flag=="in_general" ){
			$sql_result = $wpdb->get_results( " SELECT sum( interest_qty ) as total_qty FROM wp_product_interest  WHERE product_id='".$product_id."' AND interest_campaign_closed=0", OBJECT );
	}elseif( $flag=="for_interest_group" ){ 
					$sql_result = $wpdb->get_results( " SELECT SQL_CALC_FOUND_ROWS * ,  sum( interest_qty ) 
					as total_qty FROM wp_product_interest WHERE interest_group_id = ( SELECT interest_group_id from wp_product_interest WHERE product_interest_id='".$product_interest_id."' AND interest_campaign_closed=0 ) ", OBJECT ); 
					
	}
	return $sql_result;
}
/** Author: ABU TAHER, Logic-coder IT
 * make_group_name
 *@Param $product_interest_id
 * Return virtual group name for $product_interest_id with a connection of "interest_end_date"
 */ 
function make_group_name( $product_interest_id ){
	global $wpdb, $paged, $max_num_pages, $current_date, $sql_posts_total,$group_name, $product_id;	
	$sql_result = $wpdb->get_results( " SELECT * FROM wp_product_interest WHERE product_interest_id='".
	$product_interest_id."' AND interest_group_id=0", OBJECT );
	if( $sql_result ){		
		if( $sql_result[0]->interest_end_date ){
			$wp_group_data['group_name'] = date("F j, Y", $sql_result[0]->interest_end_date)."_".date("s")."_".$sql_result[0]->product_id;
			$wp_group_data['group_closing_date'] = $sql_result[0]->interest_end_date;
		}elseif( $sql_result[0]->asa_price_is_reasonable  ){ 
			$wp_group_data['group_name'] = "asap_".date("s")."_". $sql_result[0]->product_id; 
			$wp_group_data['group_closing_date'] = "asap"; 
		}
		$wp_group_data['product_id'] = $sql_result[0]->product_id;
		$format_array = array('%s', '%s', '%d'); 
		$wp_group_data['add_date'] = date("Y-m-d");
		array_push($format_array, '%s');
		$wpdb->insert( 'wp_interest_group', $wp_group_data , $format_array );
	}		
	if( $wpdb->insert_id ){ 
		global $wpdb, $paged, $max_num_pages, $current_date, $sql_posts_total;
		
		if( $sql_result[0]->interest_end_date ){
			$sql_group_result = $wpdb->get_results( " SELECT SQL_CALC_FOUND_ROWS * FROM wp_product_interest prod_int, wp_posts 
			WHERE prod_int.interest_start_date <= ( SELECT interest_end_date FROM wp_product_interest WHERE 
			product_interest_id='".$product_interest_id."' ) AND prod_int.product_id=( SELECT product_id FROM 
			wp_product_interest WHERE product_interest_id='".$product_interest_id."' ) AND prod_int.product_id = 
			wp_posts.ID AND wp_posts.post_status='publish' AND prod_int.asa_price_is_reasonable!=1 AND prod_int.interest_group_id=0", OBJECT );
		}elseif( $sql_result[0]->asa_price_is_reasonable  ){ 
			$sql_group_result = $wpdb->get_results( " SELECT SQL_CALC_FOUND_ROWS * FROM wp_product_interest prod_int, wp_posts 
			WHERE prod_int.product_id=( SELECT product_id FROM wp_product_interest WHERE product_interest_id='".$product_interest_id."' ) AND prod_int.product_id = 
			wp_posts.ID AND wp_posts.post_status='publish' AND prod_int.asa_price_is_reasonable=1 AND prod_int.interest_group_id=0", OBJECT );			
		}		
		$sql_total_interest = $wpdb->get_var( "SELECT FOUND_ROWS();" );
		if( $sql_group_result ){
			$wp_product_interest_data['interest_group_id'] = $wpdb->insert_id; 
			foreach( $sql_group_result as $sql_group_result_data ) {
				$where = array( "product_interest_id"=>$sql_group_result_data->product_interest_id );
				$where_format = array();  
				$wpdb->update( 'wp_product_interest', $wp_product_interest_data, $where, $format_array = null, $where_format = null );			
		}			
			/*$wp_interest_group_data['total_interest_qty'] = $count_interest_qty; 
			$where = array( "group_id"=>$wp_product_interest_data['interest_group_id'] );			
			$where_format = array();  
			$wpdb->update( 'wp_interest_group', $wp_interest_group_data, $where, $format_array = null, $where_format = null );	*/ 	
		}			
	}	
	return $wpdb->insert_id?$wpdb->insert_id:"";	
}
/** Author: ABU TAHER, Logic-coder IT
 * make_as_group_for_interest
 *@Param $product_interest_id
 * Return virtual group contents for $product_interest_id with a connection of "interest_end_date"
 */ 
function make_as_group_for_interest( $product_interest_id ){	
	global $wpdb, $paged, $max_num_pages, $current_date, $sql_posts_total;
	$paged = ($_GET['paged']) ? $_GET['paged'] : 1;
	$post_per_page = 2;
	$offset = ($paged - 1) * $post_per_page;	
	$sql_result = $wpdb->get_results( " SELECT SQL_CALC_FOUND_ROWS * FROM wp_product_interest prod_int, wp_posts WHERE prod_int.interest_start_date <= ( SELECT interest_end_date FROM wp_product_interest WHERE product_interest_id='".$product_interest_id."' ) AND prod_int.product_id=( SELECT product_id FROM wp_product_interest WHERE product_interest_id='".$product_interest_id."' ) AND prod_int.product_id = wp_posts.ID AND wp_posts.post_status='publish' AND prod_int.asa_price_is_reasonable!=1  LIMIT $offset,$post_per_page ", OBJECT );
	$sql_posts_total = $wpdb->get_var( "SELECT FOUND_ROWS();" );
	$max_num_pages = ceil($sql_posts_total / $post_per_page);
	return $sql_result;	
}
/** Author: ABU TAHER, Logic-coder IT
 * get_interest_group_info 
 *@param $group_id ="" 
 * Return interest_group_info 
 */ 
function get_interest_group_info( $group_id =""  ){
	global $wpdb, $paged, $max_num_pages, $current_date, $sql_posts_total;
	$paged = ($_GET['paged']) ? $_GET['paged'] : 1;
	$post_per_page = 12;
	$offset = ($paged - 1) * $post_per_page;	
	/*$sql_result = $wpdb->get_results( " SELECT SQL_CALC_FOUND_ROWS *, inte_grp.group_id FROM wp_interest_group inte_grp, wp_product_interest prod_inte WHERE prod_inte.interest_group_id=inte_grp.group_id GROUP BY inte_grp.group_id  DESC LIMIT $offset,$post_per_page ", OBJECT );*/
	if( $group_id ){
		$sql_result = $wpdb->get_results( " SELECT SQL_CALC_FOUND_ROWS * FROM wp_interest_group inte_grp, wp_product_interest prod_inte 
		WHERE inte_grp.group_id='".$group_id."' AND  inte_grp.product_id=prod_inte.product_id AND prod_inte.interest_group_id=0", OBJECT );
	}else{
		//$sql_result = $wpdb->get_results( " SELECT SQL_CALC_FOUND_ROWS *, prod_inte.interest_group_id FROM wp_product_interest prod_inte WHERE prod_inte.interest_group_id!=0 GROUP BY prod_inte.interest_group_id  DESC LIMIT $offset,$post_per_page ", OBJECT ); 
	   $sql_result = $wpdb->get_results( " SELECT SQL_CALC_FOUND_ROWS * FROM wp_interest_group  ORDER BY group_id DESC", OBJECT );
	}	
	$sql_posts_total = $wpdb->get_var( "SELECT FOUND_ROWS();" );
	$max_num_pages = ceil($sql_posts_total / $post_per_page);
	return $sql_result;	
}
/** Author: ABU TAHER, Logic-coder IT
 * get_interest_group_details
 *@Param $interest_group_id, $action="", $product_interest_id=""
 * Return Group Details
 */ 
function get_interest_group_details( $interest_group_id , $action="", $product_interest_id="" ){	
	global $wpdb, $paged, $max_num_pages, $current_date, $sql_posts_total;
	$paged = ($_GET['paged']) ? $_GET['paged'] : 1;
	$post_per_page = 12;
	$offset = ($paged - 1) * $post_per_page;	
	if( $action== "move-out" && $product_interest_id ){
			$wp_interest_data['interest_group_id'] = 0; 
			$wpdb->query(" UPDATE wp_product_interest SET interest_group_id =0 WHERE product_interest_id IN($product_interest_id) " );
}
if( $action== "add-to-group" && $product_interest_id ){
			$wp_interest_data['interest_group_id'] = $interest_group_id; 
			/*$where = array( "product_interest_id"=> $product_interest_id );			
			$where_format = array();  
			$wpdb->update( 'wp_product_interest', $wp_interest_data, $where, $format_array = null, $where_format = null );	*/ 
			$wpdb->query(	" UPDATE wp_product_interest SET interest_group_id ='".$interest_group_id."' WHERE product_interest_id IN($product_interest_id) " );
	}	
	$sql_result = $wpdb->get_results( "SELECT SQL_CALC_FOUND_ROWS * FROM wp_product_interest prod_inte, wp_interest_group inte_grp,wp_posts WHERE ( prod_inte.interest_group_id=inte_grp.group_id AND inte_grp.group_id='".$interest_group_id."' AND prod_inte.interest_confirmed= 0 AND prod_inte.interest_campaign_closed= 0 ) AND ( inte_grp.product_id=wp_posts.ID AND wp_posts.post_status='publish')  LIMIT $offset,$post_per_page", OBJECT );	
	$sql_posts_total = $wpdb->get_var( "SELECT FOUND_ROWS();" ); 
	$max_num_pages = ceil($sql_posts_total / $post_per_page);
	return $sql_result;	
}

/** Author: ABU TAHER, Logic-coder IT
 * get_group_info
 *@Param $interest_group_id
 * Return Group Details
 */ 
function get_group_info( $interest_group_id ){	
	global $wpdb;	
	$sql_result = $wpdb->get_results( "SELECT SQL_CALC_FOUND_ROWS * FROM wp_interest_group inte_grp,wp_posts WHERE ( inte_grp.group_id='".$interest_group_id."'  AND  inte_grp.product_id=wp_posts.ID AND wp_posts.post_status='publish') ", OBJECT );	
	//$sql_result = $wpdb->get_results( "SELECT SQL_CALC_FOUND_ROWS * FROM wp_product_interest prod_inte, wp_interest_group inte_grp,wp_posts WHERE ( prod_inte.interest_group_id=inte_grp.group_id AND inte_grp.group_id='".$interest_group_id."' ) AND ( inte_grp.product_id=wp_posts.ID AND wp_posts.post_status='publish') ", OBJECT );	
	return $sql_result;	
}
/** Author: ABU TAHER, Logic-coder IT
 * remove_group
 *
 *  @param $action, $group_id
 */ 
function remove_group( $action, $group_id ){
	global $wpdb;	
	$delete_rows = $wpdb->query("DELETE FROM wp_interest_group WHERE group_id ='".
	$group_id."' ");	
	return $delete_rows;
}
/** Author: ABU TAHER, Logic-coder IT
 * rename_group_to
 *
 *  @param $group_id, $group_data
 */ 
function rename_group_to( $group_id, $group_data ){
	global $wpdb, $group_exists;				
	$wpdb->get_results( "SELECT SQL_CALC_FOUND_ROWS * FROM wp_interest_group WHERE group_name ='".$group_data['group_name']."' AND group_id !='".$group_id."' ", OBJECT );	
	$group_exists = $wpdb->get_var( "SELECT FOUND_ROWS();" ); 
	if( !$group_exists ){
		$where = array( "group_id" => $group_id); 
		$format_array = array('%s');		
		return $wpdb->update( 'wp_interest_group', $group_data, $where, $format_array = null, $where_format = null );	
	}
	//return $delete_rows;
}
/** Author: ABU TAHER, Logic-coder IT
 * get_interest_confirmed_details
 *@Param $interest_group_id, $action="", $product_interest_id=""
 * Return Group Details
 */ 
function get_interest_confirmed_details( $interest_group_id , $action="", $product_interest_id="" ){	
	global $wpdb, $paged, $max_num_pages, $current_date, $sql_posts_total;
	$paged = ($_GET['paged']) ? $_GET['paged'] : 1;
	$post_per_page = 12;
	$offset = ($paged - 1) * $post_per_page;	
	if( $action== "move-out" && $product_interest_id ){
			$wp_interest_data['interest_group_id'] = 0; 
			$wpdb->query(" UPDATE wp_product_interest 
			SET interest_group_id =0,
			interest_confirmed=0,
			interest_confirmation_link_expire=0,
			payment_confirmation_link_expire=0
			WHERE product_interest_id IN($product_interest_id) " );
	}
	if( $action== "add-to-group" && $product_interest_id ){
				/*
				$wp_interest_data['interest_group_id'] = $interest_group_id; 
				$where = array( "product_interest_id"=> $product_interest_id );			
				$where_format = array();  
				$wpdb->update( 'wp_product_interest', $wp_interest_data, $where, $format_array = null, $where_format = null );	*/ 
				$wpdb->query(	" UPDATE wp_product_interest SET interest_group_id ='".$interest_group_id."' WHERE product_interest_id IN($product_interest_id) " );
	}	
	if( $action== "add_to_confirmed_list" && $product_interest_id ){
			//$wp_interest_data['interest_group_id'] = $interest_group_id; 			
			//$wp_interest_data['interest_confirmed'] = 1; 			
			$wpdb->query(	" UPDATE wp_product_interest SET interest_group_id ='".$interest_group_id."' , interest_confirmed=1 WHERE product_interest_id IN($product_interest_id) " );
	}	
	$sql_result = $wpdb->get_results( "SELECT SQL_CALC_FOUND_ROWS * FROM wp_product_interest prod_inte, wp_interest_group inte_grp,wp_posts WHERE ( prod_inte.interest_group_id=inte_grp.group_id AND inte_grp.group_id='".$interest_group_id."' AND prod_inte.interest_confirmed= 1 ) AND ( inte_grp.product_id=wp_posts.ID AND wp_posts.post_status='publish')  LIMIT $offset,$post_per_page", OBJECT );	
	$sql_posts_total = $wpdb->get_var( "SELECT FOUND_ROWS();" ); 
	$max_num_pages = ceil($sql_posts_total / $post_per_page);
	return $sql_result;	
}
/** Author: ABU TAHER, Logic-coder IT
 * get_interest_failed_details
 *@Param $interest_group_id, $action="", $product_interest_id=""
 * Return Failed Group Details
 */ 
function get_interest_failed_details( $interest_group_id , $action="", $product_interest_id="" ){	
	global $wpdb, $paged, $max_num_pages, $current_date, $sql_posts_total;
	$paged = ($_GET['paged']) ? $_GET['paged'] : 1;
	$post_per_page = 12;
	$offset = ($paged - 1) * $post_per_page;	
	if( $action== "move-out" && $product_interest_id ){
			$wp_interest_data['interest_group_id'] = 0; 
			$wpdb->query(" UPDATE wp_product_interest 
			SET interest_group_id =0,
			interest_confirmed=0,
			interest_confirmation_link_expire=0,
			payment_confirmation_link_expire=0
			WHERE product_interest_id IN($product_interest_id) " );
	}
	if( $action== "add-to-group" && $product_interest_id ){
				/*
				$wp_interest_data['interest_group_id'] = $interest_group_id; 
				$where = array( "product_interest_id"=> $product_interest_id );			
				$where_format = array();  
				$wpdb->update( 'wp_product_interest', $wp_interest_data, $where, $format_array = null, $where_format = null );	*/ 
				$wpdb->query(	" UPDATE wp_product_interest SET interest_group_id ='".$interest_group_id."' WHERE product_interest_id IN($product_interest_id) " );
	}	
	if( $action== "add_to_confirmed_list" && $product_interest_id ){
			//$wp_interest_data['interest_group_id'] = $interest_group_id; 			
			//$wp_interest_data['interest_confirmed'] = 1; 			
			$wpdb->query(	" UPDATE wp_product_interest SET interest_group_id ='".$interest_group_id."' , interest_confirmed=1 WHERE product_interest_id IN($product_interest_id) " );
	}	
	$sql_result = $wpdb->get_results( "SELECT SQL_CALC_FOUND_ROWS * FROM wp_product_interest prod_inte, wp_interest_group inte_grp,wp_posts WHERE ( prod_inte.interest_group_id=inte_grp.group_id AND inte_grp.group_id='".$interest_group_id."' AND prod_inte.interest_campaign_closed= 2 ) AND ( inte_grp.product_id=wp_posts.ID AND wp_posts.post_status='publish')  LIMIT $offset,$post_per_page", OBJECT );	
	$sql_posts_total = $wpdb->get_var( "SELECT FOUND_ROWS();" ); 
	$max_num_pages = ceil($sql_posts_total / $post_per_page);
	return $sql_result;	
} 
/** Author: ABU TAHER, Logic-coder IT
 * get_group_price_list
 *@Param $group_price_id="", $group_id, $action="" 
 * Return Group Price List
 */ 
function get_group_price_list( $group_price_id="", $group_id, $action="" ){ 
	global $wpdb, $paged, $max_num_pages, $current_date, $sql_total_price_list, $price_data_by_id;
	$paged = ($_GET['paged']) ? $_GET['paged'] : 1;
	$post_per_page = 12;
	$offset = ($paged - 1) * $post_per_page;	
	if( $action== "remove" && $group_price_id ){
		$wpdb->query(" DELETE FROM wp_interest_group_price WHERE group_price_id IN( $group_price_id ) " );
	}
	if( $action== "edit" && $group_price_id ){
		$price_data_by_id = $wpdb->get_results( "SELECT * FROM wp_interest_group_price WHERE group_price_id='".$group_price_id."'  ", OBJECT );	
	}	
	$sql_result = $wpdb->get_results( "SELECT SQL_CALC_FOUND_ROWS * FROM wp_interest_group_price WHERE group_id='".$group_id."'  ORDER BY no_of_sells ASC LIMIT $offset,$post_per_page", OBJECT );	
	if( !empty( $sql_result ) ){
		$sql_total_price_list = $wpdb->get_var( "SELECT FOUND_ROWS();" ); 
		$max_num_pages = ceil($sql_total_price_list / $post_per_page);
	}	//print_r( $sql_result ); exit;
	return $sql_result;	
}

/** Author: ABU TAHER, Logic-coder IT
 * get_group_price_list_matched
 *@Param $group_price_id="", $group_id, $action="" 
 * Return Group Price List
 */ 
function get_group_price_list_matched( $group_id, $total_qty ){ 
	global $wpdb;		
	$sql_result = $wpdb->get_results( "SELECT * FROM wp_interest_group_price WHERE  group_id='".$group_id."' AND no_of_sells= ( SELECT max(no_of_sells) FROM wp_interest_group_price WHERE group_price_id IN (SELECT group_price_id FROM wp_interest_group_price WHERE $total_qty >= no_of_sells AND group_id='".$group_id."') ) ", OBJECT );	
	return $sql_result;	
}
/** Author: ABU TAHER, Logic-coder IT
 * get_minimum_price_list
 *@Param $group_id
 * Return Minimum Group Price List
 */ 
function get_minimum_price_list( $group_id ){ 
	global $wpdb;		
	$sql_result = $wpdb->get_results( "SELECT * FROM wp_interest_group_price WHERE  group_id='".$group_id."' AND no_of_sells= ( SELECT min(no_of_sells) FROM wp_interest_group_price WHERE group_id='".$group_id."')", OBJECT );	
	return $sql_result;	
}
/** Author: ABU TAHER, Logic-coder IT
 * set_group_price 
 *
 *  @param $price_data
* return Success/Failure Message 
 */
function set_group_price( $price_data ){
	global $wpdb;	
	$format_array = array('%d', '%d', '%f', '%f', '%f',  '%s');		
	$wpdb->insert( 'wp_interest_group_price', $price_data , $format_array ); 				
	return $wpdb->insert_id;
}

/** Author: ABU TAHER, Logic-coder IT
 * update_group_price 
 *
 *  @param $price_data, $group_price_id
* return Success/Failure Message 
 */
function update_group_price( $price_data, $group_price_id ){
	global $wpdb;	
	$where = array( "group_price_id" => $group_price_id); 
	$format_array = array('%d', '%d', '%f', '%f', '%f');		
	return $wpdb->update( 'wp_interest_group_price', $price_data, $where, $format_array = null, $where_format = null );			
	//return $wpdb->insert_id ? $wpdb->insert_id: "";
}
/** Author: ABU TAHER, Logic-coder IT
 * update_interest_unit_price 
 *
 *  @param $product_interest_id, $interest_unit_price
* return Success/Failure Message 
 
function update_interest_unit_price( $product_interest_id, $interest_unit_price ){
	global $wpdb;	
	$price_data = array( "interest_unit_price" => $interest_unit_price ); 
	$where = array( "product_interest_id" => $product_interest_id); 
	$format_array = array( '%f' );		
	return $wpdb->update( 'wp_product_interest', $price_data, $where, $format_array = null, 	$where_format = null );	
}*/

/** Author: ABU TAHER, Logic-coder IT
 * send_email_to_interest_group 
 *
 *  @param $email_data, $group_details
* return Success/Failure Message 
*/
function send_email_to_interest_group( $email_data, $group_details ){
	global $wpdb, $current_user;
	$current_user = wp_get_current_user();	 
	$dear_text =""; 
	$interest_start_date = "";
	$interest_end_date = "";
	$group_price_list_text = "";  
	$same_price_to_all = 0;
	$add_date = date("Y-m-d");
	$email_sent = 0;
	$interest_confirmation_link_expire = "";	
	$interest_confirmation_link_expire_text = ""; 
	//$time_now = 
	if( $email_data['confirmation_within'] ){
		$time_now = date("Y-m-d H:i");
		$confirmation_within = $email_data['confirmation_within'] / 24; 
		$interest_confirmation_link_expire = date('Y-m-d H:i', strtotime($time_now. ' + '.$confirmation_within. 'days'));
		$expire_date_time_separation = explode( " ", $interest_confirmation_link_expire ); 
		$expire_date  = explode( "-", $expire_date_time_separation[0] ); 
		$expire_time  = explode( ":", $expire_date_time_separation[1] ); 
		$interest_confirmation_link_expire_text = mktime( $expire_time[0], $expire_time[1], 0, $expire_date[1],$expire_date[2],$expire_date[0]	); 
}
    if( $group_details ){
		$group_price_list = get_group_price_list("", $group_details[0]->group_id, "" ); 
		if( $group_price_list ){
				foreach( $group_price_list as $group_price_data ) {			
					$group_price_list_text .="<tr>
					<td><span>". $group_price_data->no_of_sells ."</span></td>
					<td><span>".$group_price_data->inmid_price ."</span></td>
					<td><span>".$group_price_data->shipping_price ."</span></td>
					</tr>"."\n\n";
				}
		}			
		foreach( $group_details as $individual_data ){ 
			/******************************************/
			$user_info =  get_userdata( $individual_data->user_id ); 
			$user_meta_info = get_user_meta( $individual_data->user_id, "" , "" ); 
			//return (print_r( $user_meta_info )); exit;
			if( $user_meta_info['first_name'][0] ){ 
				$dear_text = $user_meta_info['first_name'][0];
			}else{
				$dear_text = $user_info->display_name; 
			}
			if( $individual_data->interest_start_date ){
				$interest_start_date = date("Y-m-d", $individual_data->interest_start_date );
				$interest_end_date = date("Y-m-d", $individual_data->interest_end_date );
			}else{ $interest_start_date = __("As soon as price is reasonable"); }
			//////////////////////////////////////////////	
			$subject="!NMID: ".$email_data["email_subject"]." CaseNo(".$group_details[0]->group_id ."_".$individual_data->product_interest_id .")\n\n";
			$message  = "<html><body>"."\n";
			$message .="<table cellpadding='0' cellspacing='0' bgcolor=#319d00 width='100%' style='margin:0 auto'><tr style='font-family: Verdana,Arial,Helvetica,sans-serif; font-size: 11px; color: rgb(255,255,255); line-height: 140%;'><td width='23'></td><td><span>!NMID: A Business Aggregator</span></td></tr></table>"."\n\n";
			$message .="<p>Dear Customer &nbsp;".$dear_text.",</p>"."\n"; 
			$message .="<p>".$email_data["email_message_to_interest_grp"]."</p>"."\n";			
			
			if( $email_data["same_price_to_all"] || !intval( $individual_data->interest_unit_price )){		
				if( $email_data["same_price_to_all"] ){
					$same_price_to_all = 1; 
				}			
				$message .="<p>A Price List is following for your interest:</p>"."\n";			
				$message .="<table cellpadding='5' cellspacing='2' bgcolor=#ffffff width='100%' style='margin:0 auto'><tr style='font-family: Verdana,Arial,Helvetica,sans-serif; font-size: 11px; color: 333333; line-height: 140%;'>
				<td><span style='font-weight:bold;'>No Of Sells</span></td>
				<td><span style='font-weight:bold;'>Unit Price</span></td>
				<td><span style='font-weight:bold;'>Shipping Price</span></td>
				</tr>"."\n\n";
				$message .= $group_price_list_text . "</table>"."\n\n";	
			}else{
				$message .="<p>The Unit Price For Your Interest Is: SEK: ".$individual_data->interest_unit_price."</p>\n";	
			}
			$message .="<p>Your Interest Details:</p>"."\n";
			$message .="<p><b>Product Name: </b><a href=".get_site_url()."/my-interest-list/?action=edit&product_interest_id=".$individual_data->product_interest_id ."&product_name=".$individual_data->post_name ." >".$individual_data->product_name ."</a></p>\n";	
			$message .="<p><b>Qty: </b>".$individual_data->interest_qty ."</p>\n";	
			$message .="<p><b>Interest Start Date: </b>".$interest_start_date."</p>\n";	
			$message .="<p><b>Interest End Date: </b>".$interest_end_date."</p>\n";				
			if( $email_data['confirmation_within'] ){
				$message .="<p>You Have&nbsp;".$email_data['confirmation_within']."Hours to confirm that you are still want to purchase this product for the above Details</p>\n";	
			}
			$message .="<p>To confirm Please click on Yes: <a href=".get_site_url()."/my-interest-list/?action=interest_confirmed&product_interest_id=".$individual_data->product_interest_id ." >Yes</a><a href=".get_site_url()."/my-interest-list/?action=interest_notconfirmed&product_interest_id=".$individual_data->product_interest_id ." >No</a>
			</p>\n";	
			$message .="<table cellpadding='0' cellspacing='0' bgcolor=#319d00 width='100%' style='margin:0 auto'><tr style='font-family: Verdana,Arial,Helvetica,sans-serif; font-size: 11px; color: rgb(255,255,255); line-height: 140%;'><td width='23'></td><td><span>!NMID: A Business Aggregator</span></td></tr></table>"."\n\n";
			$message .= "</body></html>\n";
			$uid = md5(uniqid(time()));
			$header  = "From: !NMID <".$current_user->user_email.">\r\n";
			$header .= "Reply-To:".$current_user->user_email."\r\n";
			$header .= "MIME-Version: 1.0\r\n";
			$header .= "Content-Type: multipart/mixed; boundary=\"".$uid."\"\r\n\r\n";
			$header .= "This is a multi-part message in MIME format.\r\n";
			$header .= "--".$uid."\r\n";
			$header .= "Content-type:text/html; charset=iso-8859-1\r\n";
			$header .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
			$header .= $message."\r\n\r\n";
			$header .= "--".$uid."\r\n";
			//$header .= "Content-Type: application/octet-stream; name=\"".$attachment_name."\"\r\n"; // use different content types here
			$header .= "Content-Transfer-Encoding: base64\r\n";
			//$header .= "Content-Disposition: attachment; filename=\"".$attachment_name."\"\r\n\r\n";
			//$header .= $attachedfile."\r\n\r\n";
			//echo $message; exit;
			$header .= "--".$uid."--";			
			$attachments =""; 
			$messages = ""; 		
			$email_to = $user_info->user_email; 	
			$format_array = array('%s', '%d', '%d', '%s', '%s',  '%s',  '%d',  '%s');		
			if( mail( $email_to , $subject,"",$header) )	{   
				$case_data['case_no'] = $group_details[0]->group_id ."_".$individual_data->product_interest_id;
				$case_data['product_interest_id'] = $individual_data->product_interest_id;
				$case_data['group_id'] = $group_details[0]->group_id;
				$case_data['user_id'] = $individual_data->user_id;
				$case_data['case_subject'] = $subject;
				$case_data['case_message'] = $header;
				$case_data['confirmation_within'] = $email_data['confirmation_within'];
				$case_data['same_price_to_all'] = $same_price_to_all;
				$case_data['add_date'] = $add_date;
				//print_r( $case_data ); exit;
				$succes_case_insert = insert_interest_case( $case_data , $format_array ); 
				if( !$email_sent ){ 
					$update_group_data = array( "email_sent"=>1, "same_price_to_all"=>$same_price_to_all ); 
					$where = array( "group_id"=>$group_details[0]->group_id );
					$update_format_array = array( '%d', "%d" ); 
					$where_format = array(); 
					$wpdb->update( 'wp_interest_group', $update_group_data, $where, $update_format_array = null, $where_format = null ); 					
					$email_sent = 1; 					
				}
				$update_interest_data = array( "interest_confirmation_link_expire"=>$interest_confirmation_link_expire_text ); 
					$where = array( "product_interest_id"=>$individual_data->product_interest_id );
					$update_format_array = array( '%s' ); 
					$where_format = array(); 
					$wpdb->update( 'wp_product_interest', $update_interest_data, $where, $update_format_array = null, $where_format = null ); 
			}			
			/******************************************/			
		}	
	}
	if( $email_sent ){
		return True;
	}
}

/** Author: ABU TAHER, Logic-coder IT
 * send_email_to_interest_confirmed 
 *
 *  @param $email_data, $interest_confirmed_details
* return Success/Failure Message 
*/
function send_email_to_interest_confirmed( $email_data, $interest_confirmed_details, $deal_selection ){ 
	global $wpdb, $current_user;
	$current_user = wp_get_current_user();	 
	$dear_text =""; 
	$interest_start_date = "";
	$interest_end_date = "";
	$group_price_list_text = "";  
	$same_price_to_all = 0;
	$add_date = date("Y-m-d");
	$payment_email_sent = 0;
	$payment_confirmation_link_expire = "";	
	$payment_confirmation_link_expire_text = ""; 
	$group_price_list_matched = ""; 
	$update_interest_data ="";
	$update_format_array ="";
	//$time_now = 
	if( $email_data['payment_within'] ){
		$time_now = date("Y-m-d H:i");
		$payment_within = $email_data['payment_within'] / 24; 
		$payment_confirmation_link_expire = date('Y-m-d H:i', strtotime($time_now. ' + '.$payment_within. 'days'));
		$expire_date_time_separation = explode( " ", $payment_confirmation_link_expire ); 
		$expire_date  = explode( "-", $expire_date_time_separation[0] ); 
		$expire_time  = explode( ":", $expire_date_time_separation[1] ); 
		$payment_confirmation_link_expire_text = mktime( $expire_time[0], $expire_time[1], 0, $expire_date[1],$expire_date[2],$expire_date[0]	); 
	}
    if( $interest_confirmed_details ){
		$count_interest_confirmed = count_interest_confirmed( $interest_confirmed_details[0]->product_id , $interest_confirmed_details[0]->group_id );
		//echo $count_interest_confirmed[0]->total_qty; exit; 
		//////////////////////////////////////////////////////
		$product_meta_values = get_post_meta( $interest_confirmed_details[0]->product_id, "", "" );
		$minimum_target_sells = $product_meta_values['minimum_target_sells'][0]; 
		if( $count_interest_confirmed[0]->total_qty < $minimum_target_sells ){
			$group_price_list_matched = get_minimum_price_list( $interest_confirmed_details[0]->group_id );					
		}else{
			$group_price_list_matched = get_group_price_list_matched( $interest_confirmed_details[0]->group_id, $count_interest_confirmed[0]->total_qty );
		}
		/////////////////////////////////////////////////////		 
		if( $group_price_list_matched ){
				foreach( $group_price_list_matched as $group_price_data ) {			
					$group_price_list_text .="<tr>
					<td><span>". $group_price_data->no_of_sells ."</span></td>
					<td><span>".$group_price_data->inmid_price ."</span></td>
					<td><span>".$group_price_data->shipping_price ."</span></td>
					</tr>"."\n\n";
				}
		}			
		foreach( $interest_confirmed_details as $individual_data ){ 
			/******************************************/
			$user_info =  get_userdata( $individual_data->user_id ); 
			$user_meta_info = get_user_meta( $individual_data->user_id, "" , "" ); 
			//return (print_r( $user_meta_info )); exit;
			if( $user_meta_info['first_name'][0] ){ 
				$dear_text = $user_meta_info['first_name'][0];
			}else{
				$dear_text = $user_info->display_name; 
			}
			if( $individual_data->interest_start_date ){
				$interest_start_date = date("Y-m-d", $individual_data->interest_start_date );
				$interest_end_date = date("Y-m-d", $individual_data->interest_end_date );
			}else{ $interest_start_date = __("As soon as price is reasonable"); }
			//////////////////////////////////////////////	
			$subject="!NMID: ".$email_data["email_subject"]." CaseNo(".$interest_confirmed_details[0]->group_id ."_".$individual_data->product_interest_id .")\n\n";
			$message  = "<html><body>"."\n";
			$message .="<table cellpadding='0' cellspacing='0' bgcolor=#319d00 width='100%' style='margin:0 auto'><tr style='font-family: Verdana,Arial,Helvetica,sans-serif; font-size: 11px; color: rgb(255,255,255); line-height: 140%;'><td width='23'></td><td><span>!NMID: A Business Aggregator</span></td></tr></table>"."\n\n";
			$message .="<p>Dear Customer &nbsp;".$dear_text.",</p>"."\n"; 
			$message .="<p>".$email_data["email_message_to_interest_grp"]."</p>"."\n";			
			if( $deal_selection=="want_to_deal" ){
				if( $individual_data->same_price_to_all || !intval( $individual_data->interest_unit_price ) ){		
					//$same_price_to_all = 1; 
					$message .="<p>A Price List is following for your interest:</p>"."\n";			
					$message .="<table cellpadding='5' cellspacing='2' bgcolor=#ffffff width='100%' style='margin:0 auto'><tr style='font-family: Verdana,Arial,Helvetica,sans-serif; font-size: 11px; color: 333333; line-height: 140%;'>
					<td><span style='font-weight:bold;'>No Of Sells</span></td>
					<td><span style='font-weight:bold;'>Unit Price</span></td>
					<td><span style='font-weight:bold;'>Shipping Price</span></td>
					</tr>"."\n\n";
					$message .= $group_price_list_text . "</table>"."\n\n";	
					}else{
						$message .="<p>The Unit Price For Your Interest Is: SEK: ".$individual_data->interest_unit_price."</p>\n";	
					}
				}			
			$message .="<p>Your Interest Details:</p>"."\n";
			$message .="<p><b>Product Name: </b><a href=".get_site_url()."/my-interest-list/?action=edit&product_interest_id=".$individual_data->product_interest_id ."&product_name=".$individual_data->post_name ." >".$individual_data->product_name ."</a></p>\n";	
			$message .="<p><b>Qty: </b>".$individual_data->interest_qty ."</p>\n";	
			$message .="<p><b>Interest Start Date: </b>".$interest_start_date."</p>\n";	
			$message .="<p><b>Interest End Date: </b>".$interest_end_date."</p>\n";				
			if( $deal_selection=="want_to_deal"){
				if( $email_data['payment_within'] ){
					$message .="<p>You Have&nbsp;".$email_data['payment_within']."Hours For Payment to confirm that you are still want to purchase this product for the above Details</p>\n";	
				}
				$message .="<p>For Payment Please click on This Link: <a href=".get_site_url()."/my-interest-list/?action=interest_confirmed&product_interest_id=".$individual_data->product_interest_id ." >Yes</a>
				</p>\n";	
			} 
			$message .="<table cellpadding='0' cellspacing='0' bgcolor=#319d00 width='100%' style='margin:0 auto'><tr style='font-family: Verdana,Arial,Helvetica,sans-serif; font-size: 11px; color: rgb(255,255,255); line-height: 140%;'><td width='23'></td><td><span>!NMID: A Business Aggregator</span></td></tr></table>"."\n\n";
			$message .= "</body></html>\n";
			$uid = md5(uniqid(time()));
			$header  = "From: !NMID <".$current_user->user_email.">\r\n";
			$header .= "Reply-To:".$current_user->user_email."\r\n";
			$header .= "MIME-Version: 1.0\r\n";
			$header .= "Content-Type: multipart/mixed; boundary=\"".$uid."\"\r\n\r\n";
			$header .= "This is a multi-part message in MIME format.\r\n";
			$header .= "--".$uid."\r\n";
			$header .= "Content-type:text/html; charset=iso-8859-1\r\n";
			$header .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
			$header .= $message."\r\n\r\n";
			$header .= "--".$uid."\r\n";
			//$header .= "Content-Type: application/octet-stream; name=\"".$attachment_name."\"\r\n"; // use different content types here
			$header .= "Content-Transfer-Encoding: base64\r\n";
			//$header .= "Content-Disposition: attachment; filename=\"".$attachment_name."\"\r\n\r\n";
			//$header .= $attachedfile."\r\n\r\n";
			//echo $message; 
			$header .= "--".$uid."--";			
			$attachments =""; 
			$messages = ""; 		
			$email_to = $user_info->user_email; 	
			$update_format_array = array( '%s', "%s", "%s" ); 
			if( mail( $email_to , $subject,"",$header) )	{   							
				//$case_data['product_interest_id'] = $individual_data->product_interest_id;				
				$update_case_data['payment_subject'] = $subject;
				$update_case_data['payment_message'] = $header;
				$update_case_data['payment_within'] = $email_data['payment_within'];
				$where = array( "product_interest_id"=>$individual_data->product_interest_id );
				$where_format = array(); 
				$wpdb->update( 'wp_interest_group_case', $update_case_data, $where, $update_format_array = null, $where_format = null ); 
				//print_r( $case_data ); exit;
				//$succes_case_insert = insert_interest_case( $case_data , $format_array ); 
				if( !$email_sent ){ 
					$update_group_data = array( "payment_email_sent"=>1 ); 
					$where = array( "group_id"=>$interest_confirmed_details[0]->group_id );
					$update_format_array = array( '%d' ); 
					$where_format = array(); 
					$wpdb->update( 'wp_interest_group', $update_group_data, $where, $update_format_array = null, $where_format = null ); 					
					$email_sent = 1; 					
				} 			
				if( $deal_selection=="want_to_deal" ){
					$update_interest_data = array( "interest_campaign_closed"=>0 , "payment_confirmation_link_expire"=>$payment_confirmation_link_expire_text ); 
					$update_format_array = array( '%d', '%s' ); 
				}elseif( $deal_selection=="dealings_fail" ){ 
					$update_interest_data = array( "interest_confirmed"=>0 , "interest_campaign_closed"=>2 ,"interest_confirmation_link_expire"=> 0 ); 				
					$update_format_array = array( '%d', '%d', '%s' ); 
				}
				$where = array( "product_interest_id"=>$individual_data->product_interest_id );
				$where_format = array(); 
				$wpdb->update( 'wp_product_interest', $update_interest_data, $where, $update_format_array = null, $where_format = null ); 
			}			
			/******************************************/			
		}	
	}
	if( $email_sent ){
		return True;
	}
}
/** Author: ABU TAHER, Logic-coder IT
 * insert_interest_case 
 *
 *  @param $case_data, $format_array
* return Success/Failure Message 
 */
function insert_interest_case( $case_data, $format_array ){
	global $wpdb;	
	$wpdb->insert( 'wp_interest_group_case', $case_data , $format_array ); 				
	return $wpdb->case_id;
}


/*********  Product Form Validation  for Add My Interest **********/
/** Author: ABU TAHER, Logic-coder IT
 *
* return Success/Error Message
 */
function product_form_validation( &$product_data , &$fileData ){ 
	global $product_interest_validation_errors;
	$product_interest_validation_errors = new WP_Error(); 
	if( empty( $product_data['post_title'] ) ){	
		$product_interest_validation_errors->add('empty_post_title', __('Product name should not be empty!!!' , TEXTDOMAIN) );	
	}
	if ( $fileData['name'] ) {
		$imageInfo = @getimagesize($fileData['tmp_name']);
		if (false === $imageInfo || !product_imageIsRightSize($imageInfo[0], $imageInfo[1])) {
			$product_interest_validation_errors->add('image_size_error', __('Image Size Error:', TEXTDOMAIN) );			
			$product_interest_validation_errors->add('image_min_width', __('Minimum Image Width: 50px', TEXTDOMAIN) );			
			$product_interest_validation_errors->add('image_min_height', __('Minimum Image Height: 50px', TEXTDOMAIN) );
			$product_interest_validation_errors->add('image_max_width', __('Maximum Image Width: 2500px', TEXTDOMAIN) );			 $product_interest_validation_errors->add('image_max_height', __('Maximum Image Height: 2500px', TEXTDOMAIN) );
		}
	}	
	//return $product_interest_validation_errors; 
	//print_r( $product_interest_validation_errors ); exit;	
}
/*********  Interest Form Validation  for Add My Interest **********/
/** Author: ABU TAHER, Logic-coder IT
 *
* return Success/Error Message
 */
function interest_form_validation(  ){ 
	global $product_interest_validation_errors, $wp_interest_form_data, $interest_start_date_time_as_text, $interest_start_date, $interest_end_date, $product_attributes;
	$current_user_id = get_current_user_id();			
	$user_info = get_userdata($current_user_id);
	$all_meta_for_user = get_user_meta( $current_user_id );
	if($user_info->roles){
		$current_user_role = implode(', ', $user_info->roles) ;
	}
	$product_interest_meta_insert_id = "";
	$wp_interest_form_data['asa_price_is_reasonable'] = stripslashes_deep($_POST['asa_price_is_reasonable']);
	$interest_start_date = stripslashes_deep($_POST['interest_start_date']);
	$wp_interest_form_data['interest_start_date'] = $interest_start_date;
	$wp_interest_form_data['interest_date_range'] = stripslashes_deep($_POST['interest_date_range']);
	$wp_interest_form_data['interest_qty'] = stripslashes_deep($_POST['interest_qty']);
	$wp_interest_form_data['interest_notes'] = stripslashes_deep($_POST['interest_notes']); 
	$wp_interest_form_data['interest_recuring_purchase'] = stripslashes_deep($_POST['interest_recuring_purchase']);
	$wp_interest_form_data['subscribe_this_product'] = stripslashes_deep($_POST['subscribe_this_product']);
	// Form validation For all types of user
	// Validate interest time duration
	if( empty( $wp_interest_form_data['asa_price_is_reasonable'] ) && empty( $wp_interest_form_data['interest_start_date'] ) ){
		$product_interest_validation_errors->add('empty_interest_time_duration', __('Please choose interest time duration!!!', TEXTDOMAIN) );
	}
	if( !empty( $wp_interest_form_data['asa_price_is_reasonable'] ) && !empty( $wp_interest_form_data['interest_start_date'] ) ){
		$product_interest_validation_errors->add('empty_interest_time_duration', __('Please choose only one interest time duration!!!', TEXTDOMAIN ) );
	}
	if( empty( $wp_interest_form_data['asa_price_is_reasonable'] ) && !empty( $wp_interest_form_data['interest_start_date'] )){
		$interest_start_date_arr = explode("-", $wp_interest_form_data['interest_start_date'] );
		$interest_start_date_time_as_text = mktime(0, 0, 0, ($interest_start_date_arr[1]), $interest_start_date_arr[2], $interest_start_date_arr[0]);
		$wp_interest_form_data['interest_start_date'] = $interest_start_date_time_as_text; 
		$interest_start_date_deafult = date('Y-m-d', strtotime(date('Y-m-d'). ' + 14 days'));
		$interest_start_date_deafult_arr = explode("-",$interest_start_date_deafult);
		$interest_start_date_deafult_text = mktime(0, 0, 0, ($interest_asa_price_is_reasonablestart_date_deafult_arr[1]), $interest_start_date_deafult_arr[2], $interest_start_date_deafult_arr[0]);
		if( $interest_start_date_time_as_text < $interest_start_date_deafult_text ){
			$product_interest_validation_errors->add('empty_interest_time_duration', __('Interest starting date should be '.$interest_start_date_deafult. ' Or higher!!!' , TEXTDOMAIN ) );	
		}   
	}
	// Validate interest_date_range
	if( empty( $wp_interest_form_data['asa_price_is_reasonable'] ) ){
		if( empty( $wp_interest_form_data['interest_date_range'] ) ){	
			$product_interest_validation_errors->add('empty_interest_date_range', __('Interest To should not be empty!!!', TEXTDOMAIN ) );	
		}
	}
	// Validate interest Qty
	if( empty( $wp_interest_form_data['interest_qty'] ) ){	
		$product_interest_validation_errors->add('empty_interest_qty', __('Interest Quantity should not be empty!!!', TEXTDOMAIN ) );	
	}elseif(!ctype_digit( $wp_interest_form_data['interest_qty'] ) ){
		$product_interest_validation_errors->add('invalid_interest_qty', __('Interest Quantity should be a number!!!', TEXTDOMAIN ) );	
	}
	// Form validation >> Visitor
	if ( !is_user_logged_in() ) { 
	//$wp_interest_form_data['user_id'] = '10';
	$wp_interest_form_data['interest_visitor_email'] = stripslashes_deep($_POST['interest_visitor_email']);
	$wp_interest_form_data['interest_visitor_phone'] = stripslashes_deep($_POST['interest_visitor_phone']);
	// Validate E-mail
	if ( empty( $wp_interest_form_data['interest_visitor_email'] ) ) {
		$product_interest_validation_errors->add('empty_interest_visitor_email', __('E-mail can not be empty!!!', TEXTDOMAIN ) );
	}
	if ( !empty( $wp_interest_form_data['interest_visitor_email'] ) && !is_email( $wp_interest_form_data['interest_visitor_email'] ) ) {
		$product_interest_validation_errors->add('invalid_interest_visitor_email', __('Invalid E-mail, please choose another one!!!', TEXTDOMAIN ) );
	}
	if ( ! defined( 'WP_IMPORTING' ) && email_exists( $wp_interest_form_data['interest_visitor_email'] ) ) {
		$product_interest_validation_errors->add( 'existing_interest_visitor_email', __( 'Sorry, that email address is already used!!!' , TEXTDOMAIN ) );
	}			
	// Validate Phone
	if ( empty( $wp_interest_form_data['interest_visitor_phone'] ) ) {
		$product_interest_validation_errors->add('empty_interest_visitor_phone', __('Phone can not be empty!!!', TEXTDOMAIN ) );
	}elseif( !preg_match("/[^0-9]/", $wp_interest_form_data['interest_visitor_phone'] ) && strlen($wp_interest_form_data['interest_visitor_phone'] ) !=10 ){
	//$interest_validation_errors->add('invalid_interest_visitor_phone', __('Please enter valid phone number!!!') );
		}
	}
	//Form Validation >> User role company
	if($current_user_role=="company"){
		//$authorative_person = stripslashes_deep($_POST['authorative_person']);
		$wp_interest_form_data['authorative_person'] = stripslashes_deep($_POST['authorative_person']);
		// Validate authorative person
		if(empty( $wp_interest_form_data['authorative_person'] ) ){
			$product_interest_validation_errors->add('empty_authorative_person', __('Please choose Authoritative person for this purchase interest!!!', TEXTDOMAIN ) );
		}elseif( $wp_interest_form_data['authorative_person'] =="authorative_person_one"){
			$wp_interest_form_data['authorative_person_first_name'] = $all_meta_for_user['authorative_person_one_first_name'][0];
			$wp_interest_form_data['authorative_person_last_name'] = $all_meta_for_user['authorative_person_one_last_name'][0];
			$wp_interest_form_data['authorative_person_email'] = $all_meta_for_user['authorative_person_one_email'][0];
			$wp_interest_form_data['authorative_person_phone'] = $all_meta_for_user['authorative_person_one_phone'][0];
			if(empty($all_meta_for_user['authorative_person_one_first_name'][0])){
				$product_interest_validation_errors->add('empty_authorative_person_one_first_name', __('Authoritative person first name can&rsquo;t be empty!!!', TEXTDOMAIN ) );
			}if(empty($all_meta_for_user['authorative_person_one_last_name'][0])){
				$product_interest_validation_errors->add('empty_authorative_person_one_last_name', __('Authoritative person last name can&rsquo;t be empty!!!', TEXTDOMAIN ) );
			}if(empty($all_meta_for_user['authorative_person_one_phone'][0])){
				$product_interest_validation_errors->add('empty_authorative_person_one_phone', __('Authoritative person phone can&rsquo;t be empty!!!', TEXTDOMAIN ) );
			}if(empty($all_meta_for_user['authorative_person_one_email'][0])){
				$product_interest_validation_errors->add('empty_authorative_person_one_email', __('Authoritative person E-mail can&rsquo;t be empty!!!', TEXTDOMAIN ) );
			}							
		}elseif( $wp_interest_form_data['authorative_person'] =="authorative_person_two"){
			$wp_interest_form_data['authorative_person_first_name'] = $all_meta_for_user['authorative_person_two_first_name'][0];
			$wp_interest_form_data['authorative_person_last_name'] = $all_meta_for_user['authorative_person_two_last_name'][0];
			$wp_interest_form_data['authorative_person_email'] = $all_meta_for_user['authorative_person_two_email'][0];
			$wp_interest_form_data['authorative_person_phone'] = $all_meta_for_user['authorative_person_two_phone'][0];
			if(empty($all_meta_for_user['authorative_person_two_first_name'][0])){
				$product_interest_validation_errors->add('empty_authorative_person_two_first_name', __('Authoritative person first name can&rsquo;t be empty!!!', TEXTDOMAIN ) );
			}if(empty($all_meta_for_user['authorative_person_two_last_name'][0])){
				$product_interest_validation_errors->add('empty_authorative_person_two_last_name', __('Authoritative person last name can&rsquo;t be empty!!!', TEXTDOMAIN ) );
			}if(empty($all_meta_for_user['authorative_person_two_phone'][0])){
				$product_interest_validation_errors->add('empty_authorative_person_two_phone', __('Authoritative person phone can&rsquo;t be empty!!!', TEXTDOMAIN ) );
			}if(empty($all_meta_for_user['authorative_person_two_email'][0])){
				$product_interest_validation_errors->add('empty_authorative_person_two_email', __('Authoritative person E-mail can&rsquo;t be empty!!!', TEXTDOMAIN ) );
			}
		}elseif( $wp_interest_form_data['authorative_person'] =="authorative_person_three"){
			$wp_interest_form_data['authorative_person_first_name'] = $all_meta_for_user['authorative_person_three_first_name'][0];
			$wp_interest_form_data['authorative_person_last_name'] = $all_meta_for_user['authorative_person_three_last_name'][0];
			$wp_interest_form_data['authorative_person_email'] = $all_meta_for_user['authorative_person_one_email'][0];
			$wp_interest_form_data['authorative_person_phone'] = $all_meta_for_user['authorative_person_three_phone'][0];
			if(empty($all_meta_for_user['authorative_person_three_first_name'][0])){
				$product_interest_validation_errors->add('empty_authorative_person_three_first_name', __('Authoritative person first name can&rsquo;t be empty!!!', TEXTDOMAIN ) );
			}if(empty($all_meta_for_user['authorative_person_three_last_name'][0])){
				$product_interest_validation_errors->add('empty_authorative_person_three_last_name', __('Authoritative person last name can&rsquo;t be empty!!!', TEXTDOMAIN ) );
			}if(empty($all_meta_for_user['authorative_person_three_phone'][0])){
				$product_interest_validation_errors->add('empty_authorative_person_three_phone', __('Authoritative person phone can&rsquo;t be empty!!!', TEXTDOMAIN ) );
			}if(empty($all_meta_for_user['authorative_person_three_email'][0])){
				$product_interest_validation_errors->add('empty_authorative_person_three_email', __('Authoritative person E-mail can&rsquo;t be empty!!!', TEXTDOMAIN ) );
			}
		}	
	}				
}

function product_interest_insert( $product_id ){ 
		// Prepare Data to insert in wp_product_interest: Table and wp_product_interest_meta:Table
		global $wp_interest_form_data, $interest_start_date_time_as_text, $interest_start_date, $interest_end_date, $product_attributes, $product_data;
		$product_attributes = get_field('attributes');
		$wp_interest_form_data['user_id'] = $product_data['post_author']; 
		$wp_interest_form_data['product_id'] = $product_id;
		$wp_interest_form_data['product_name'] = $product_data['post_title'];
		if( $wp_interest_form_data['asa_price_is_reasonable'] ){
			$wp_interest_form_data['asa_price_is_reasonable'] = 1;
			$wp_interest_form_data['interest_start_date'] = "";
			$wp_interest_form_data['interest_date_range'] = "";
			$wp_interest_form_data['interest_end_date'] = "";	
		}elseif( $interest_start_date_time_as_text ){ 		
			$interest_end_date = date('Y-m-d', strtotime($interest_start_date. ' + '.$wp_interest_form_data['interest_date_range']. 'days'));
			$interest_end_date_arr = explode("-",$interest_end_date);
			$interest_end_date_time_as_text = mktime(0, 0, 0, ($interest_end_date_arr[1]), $interest_end_date_arr[2], $interest_end_date_arr[0]);
			$wp_interest_form_data['interest_end_date'] = $interest_end_date_time_as_text;
		}					
		if ( !is_user_logged_in() ) { 
			$wp_product_interest_data['user_id'] = "visitor"; 			
		}		
		$wp_interest_form_data['add_date'] = date("Y-m-d");
		if( $product_attributes ) { 
			foreach( $product_attributes as $product_attribute ) {
				$product_attr[] = array('name' => $product_attribute['label'], 'value' => $_POST[$product_attribute['label']]);			
			}							
		}				
		if( empty( $product_interest_id ) ){
			$product_interest_insert_id = wp_product_interest_insert( $wp_interest_form_data, $format_array );
			if( $product_interest_insert_id && sizeof($product_attr) > 0 ){
				$product_interest_meta_insert_id = wp_product_interest_meta_insert( $product_interest_insert_id, $wp_interest_form_data, $product_attr );
			}	
		}
		if( !empty( $product_interest_id ) && !$my_interest_meta_data[0]->interest_confirmed ){ 
			$product_interest_update_id = wp_product_interest_update( $wp_interest_form_data, $format_array, $product_interest_id ); 
			$product_interest_meta_update_id = wp_product_interest_meta_update( $product_interest_id, $wp_interest_form_data, $product_attr );						
		}
		if( $product_interest_insert_id  ){
			echo '<div class="notice">Congratulations!!! Product '.$wp_interest_form_data['product_name'].' was successfully added to your interest list! <span class="close"></span></div>';
		}elseif( $product_interest_update_id || $product_interest_meta_update_id ){
			echo '<div class="notice">Congratulations!!! Product '.$wp_interest_form_data['product_name'].' was successfully updated to your interest list! <span class="close"></span></div>';
		}
		if ( $my_interest_meta_data[0]->interest_confirmed  ){
			echo '<div class="notice">Sorry!!! You Don&rsquo;t Have Permission To This Action As You Already Confirmed Your Purchase Interest</div>';
		}
		
		if( $product_interest_id ){
			$valid_action = wp_check_valid_user_action( $current_user_id, $product_interest_id, "product_interest"); 
			if($valid_action){
				$my_interest_meta_data = wp_my_interest_meta( $current_user_id, $product_interest_id );
				if( $my_interest_meta_data ){
					foreach( $my_interest_meta_data as $my_interest_meta ){
						$interest_meta_array[$my_interest_meta->meta_name] = $my_interest_meta->meta_value; 
					}
				}
			}
		}
}
/*********  Show interest form for a single product **********/
/** Author: ABU TAHER, Logic-coder IT
 * Display interest form for single product 
 *
* return interest form for single product 
 */
add_action('inmid_product_interest_form', 'inmid_product_interest_form_function');
function inmid_product_interest_form_function(){ 	
//get_template_part('woocommerce/single-product/product', 'attributes_inmid');	
global $interest_form, $show_this_div, $interest_start_date_time_as_text, $interest_validation_errors, $interest_meta_array, $format_array, $wp_interest_form_data;
$show_this_div = 0; 
$interest_start_date_time_as_text = '';
$interest_validation_errors = new WP_Error(); 		
$interest_meta_array = array();
$format_array = array(); 
/*$wp_interest_form_data['asa_price_is_reasonable'] = '';
$wp_interest_form_data['interest_start_date'] = '';
$wp_interest_form_data['interest_date_range'] = '';
$wp_interest_form_data['interest_qty'] = '';
$wp_interest_form_data['interest_notes'] = '';
$wp_interest_form_data['interest_recuring_purchase'] ='' ;
$wp_interest_form_data['authorative_person'] = '';*/
//////////////////////////////////////////////////////////////
	
////////////////////////////////////////////////////////////	
	if( isset( $wp_interest_form_data['interest_start_date'] )  && $wp_interest_form_data['interest_start_date'] ) { 
		$interest_start_date_deafult = date('Y-m-d' , $wp_interest_form_data['interest_start_date'] ); 
	} else { 
		$today_date = date('Y-m-d');
		$interest_start_date_deafult = date('Y-m-d', strtotime($today_date. ' + 14 days'));
	} 
	$all_meta_for_user = get_user_meta( get_current_user_id() );
	//print_r( $all_meta_for_user ); exit;
	$user_info = get_userdata( get_current_user_id() );
	if( $user_info->roles ){
		$current_user_role = implode(', ', $user_info->roles) ;
	}
	$checked = ''; 
	if( isset( $wp_interest_form_data['asa_price_is_reasonable'] ) && $wp_interest_form_data['asa_price_is_reasonable']  ) { 
		$checked= 'checked'; 
		$disabled = 'disabled';		
	}
	$interest_form = array( 
		array(	'label' => __('När planerar du köpet?' ,TEXTDOMAIN ), 
					'name'=>'when_plan_to_purchase', 
					'id'=>'when_plan_to_purchase', 
					'type'=>'label'			
		),
		array(	'label' => __('Så fort priset är rimligt', TEXTDOMAIN ), 
					'name'=>'asa_price_is_reasonable', 
					'id'=>'asa_price_is_reasonable', 
					'type'=>'checkbox',		
					'checked' => $checked
		), 
		array(	'label' => __('Eller' , TEXTDOMAIN  ), 
					'name'=>'or', 
					'id'=>'or', 
					'type'=>'label'
		), 
		array(	'label' => __('Från' , TEXTDOMAIN  ), 
					'name'=>'interest_start_date', 
					'id'=>'interest_start_date', 
					'type'=>'text',
					'value' => $interest_start_date_deafult,
					'attribute' => 'readonly',
					'disabled' => $disabled		
		),
	    array(	'label' => __('Till' , TEXTDOMAIN  ), 
			'name'=>'interest_date_range', 
			'id'=>'interest_date_range', 
			'type'=>'select',
			'disabled' => $disabled,
			'options' => array(	array( 'label' =>__('15 Dagar' , TEXTDOMAIN  ),  'value' => '15' ),
											array( 'label' =>__('30 Dagar' , TEXTDOMAIN  ),  'value' => '30'),
											array( 'label' =>__('45 Dagar' , TEXTDOMAIN  ),  'value' => '45'),
											array( 'label' =>__('60 Dagar' , TEXTDOMAIN  ),  'value' => '60'),
											array( 'label' =>__('90 Dagar' , TEXTDOMAIN  ),  'value' => '90')
								)			
		), 		
		array(	'label' => __('Antal Styck', TEXTDOMAIN ), 
					'name'=>'interest_qty', 
					'id'=>'interest_qty', 
					'type'=>'text',
					'value' =>$wp_interest_form_data['interest_qty'],
					'attribute' => ''
		)				
	);		
	if ( !is_user_logged_in() ) {	
	array_push( $interest_form, array(	'label' => __('Mejl', TEXTDOMAIN ), 
															'name'=>'interest_visitor_email', 
															'id'=>'interest_visitor_email', 
															'type'=>'text',
															'value' =>$wp_interest_form_data['interest_visitor_email'],
															'attribute' => ''													)
					);
		array_push( $interest_form, array(	'label' => __('Telefon', TEXTDOMAIN ), 
																'name'=>'interest_visitor_phone', 
																'id'=>'interest_visitor_phone', 
																'type'=>'text',
																'value' => $wp_interest_form_data['interest_visitor_phone'],
																'attribute' => ''
													)
						);
	}	
	if($current_user_role==='company'){
		$authorative_person_options = array();
		if( $all_meta_for_user['authorative_person_one_first_name'][0] ) { 
			$label_value = $all_meta_for_user['authorative_person_one_first_name'][0]. '&nbsp;'.$all_meta_for_user['authorative_person_one_last_name'][0]; 
			array_push( $authorative_person_options, array( 'label' =>$label_value, 'value' => 'authorative_person_one' ) );
		}
		if( $all_meta_for_user['authorative_person_two_first_name'][0] || $all_meta_for_user['authorative_person_two_last_name'][0] ) {
			$label_value = $all_meta_for_user['authorative_person_two_first_name'][0]. '&nbsp;'.$all_meta_for_user['authorative_person_two_last_name'][0]; 
			array_push( $authorative_person_options, array( 'label' =>$label_value, 'value' => 'authorative_person_two' ) );
		}
		if( $all_meta_for_user['authorative_person_three_first_name'][0] || $all_meta_for_user['authorative_person_three_last_name'][0] ) {
			$label_value = $all_meta_for_user['authorative_person_three_first_name'][0]. '&nbsp;'.$all_meta_for_user['authorative_person_three_last_name'][0]; 
			array_push( $authorative_person_options, array( 'label' =>$label_value, 'value' => 'authorative_person_three' ) );
		}
		array_push( $interest_form, array(	'label' => __('Auktoritativ Person' , TEXTDOMAIN  ), 
																'name'=>'authorative_person', 
																'id'=>'authorative_person', 
																'type'=>'select',
																'options' => $authorative_person_options 
													)  
						); 
	}
	array_push( $interest_form, array(	'label' => __('Beskriv den önskade produkten' , TEXTDOMAIN  ), 
															'name'=>'interest_notes', 
															'id'=>'interest_notes', 
															'type'=>'textarea',
															'placeholder' =>__('förklaring: egna önskemål om produktens utseende, ålder m.m', TEXTDOMAIN ),
															'value' => $wp_interest_form_data['interest_notes']
												) 
					); 
	array_push( $interest_form, 	array( 	'label' => __('Återkommande Köp' , TEXTDOMAIN  ), 
															'name'=>'interest_recuring_purchase', 
															'id'=>'interest_recuring_purchase', 
															'type'=>'select',
															'options' =>array(	array( 'label' =>__('Månadsvis' , TEXTDOMAIN  ),  'value' =>  __('Månadsvis' , TEXTDOMAIN  ) ),
																							array( 'label' =>__('Varje 2 Månad' , TEXTDOMAIN  ),  'value' => __('Varje 2 Månad' , TEXTDOMAIN  ) ),
																							array( 'label' =>__('Varje 3 Månad' , TEXTDOMAIN  ),  'value' => __('Varje 3 Månad' , TEXTDOMAIN  ) ),
																							array( 'label' =>__('Årlig' , TEXTDOMAIN  ),  'value' => __('Årlig' , TEXTDOMAIN  ) ),
																							array( 'label' =>__('Varje 2 år' , TEXTDOMAIN  ),  'value' => __('Varje 2 år' , TEXTDOMAIN  ) ),
																							array( 'label' =>__('Varje 3 år' , TEXTDOMAIN  ),  'value' => __('Varje 3 år' , TEXTDOMAIN  ) ),
																							array( 'label' =>__('Varje 4 år' , TEXTDOMAIN  ),  'value' => __('Varje 4 år' , TEXTDOMAIN  ) ),
																							array( 'label' =>__('Varje 5 år' , TEXTDOMAIN  ),  'value' => __('Varje 5 år' , TEXTDOMAIN  ) ),
																							array( 'label' =>__('Varje 6 år' , TEXTDOMAIN  ),  'value' => __('Varje 6 år' , TEXTDOMAIN  ) ),
																							array( 'label' =>__('Varje 7 år' , TEXTDOMAIN  ),  'value' => __('Varje 7 år' , TEXTDOMAIN  ) ),
																							array( 'label' =>__('Varje 8 år' , TEXTDOMAIN  ),  'value' => __('Varje 8 år' , TEXTDOMAIN  ) ),
																							array( 'label' =>__('Varje 9 år' , TEXTDOMAIN  ),  'value' => __('Varje 9 år' , TEXTDOMAIN  ) ),
																							array( 'label' =>__('Varje 10 år' , TEXTDOMAIN  ),  'value' => __('Varje 10 år' , TEXTDOMAIN  ) )
																							
																				)
												) 
					); 	
	array_push( $interest_form, 	array(	'label' => __('Prenumerera här produkten' , TEXTDOMAIN  ), 
															'name'=>'subscribe_this_product', 
															'id'=>'subscribe_this_product', 
															'type'=>'select',
															'options' =>array(	array( 'label' =>__('50pcs' , TEXTDOMAIN  ),  'value' => '50'),
																							array( 'label' =>__('100pcs' , TEXTDOMAIN  ),  'value' => '100'),
																							array( 'label' =>__('300pcs' , TEXTDOMAIN  ),  'value' => '300'),
																							array( 'label' =>__('500pcs' , TEXTDOMAIN  ),  'value' => '500'),
																							array( 'label' =>__('1000pcs' , TEXTDOMAIN  ),  'value' => '1000'),
																							array( 'label' =>__('Others' , TEXTDOMAIN  ),  'value' => 'others')														
																				)
												)
					);  	
	array_push( $interest_form, 	array(	'label' => __('specificera andra' , TEXTDOMAIN  ), 
															'name'=>'subscribe_this_product_others', 
															'id'=>'subscribe_this_product_others', 
															'type'=>'text',
															'value' => $wp_interest_form_data['subscribe_this_product']
												)
					);  	
return $interest_form;
}

/*********  Show Product AND Interest form for Add My Interest **********/
/** Author: ABU TAHER, Logic-coder IT
 * Display Product AND Interest form for Add My Interest
 *
* return Product AND Interest form for Add My Interest
 */
add_shortcode('add_my_product_interest', 'add_my_product_interest_inmid');
function add_my_product_interest_inmid(){ 	
global $product_options, $wp_interest_form_data, $product_interest_validation_errors, $product_data; 
$product_options['max-image-width'] = '2500';
$product_options['min-image-width'] = '50';
$product_options['max-image-height'] = '2500';
$product_options['min-image-height'] = '50';

if( isset( $_POST['add_product_interest'] ) ){ 
	$product_post_meta_IsSubmission = 'is_submission';
	$product_post_meta_SubmitterIp  = 'user_submit_ip';
	$product_post_meta_Submitter    = 'user_submit_name';
	$product_data = array(); 	
	$product_data['post_title'] = wp_strip_all_tags( $_POST['post_title'] ) ; 
	$product_data['post_content'] =  stripslashes_deep( $_POST['post_content'] ); 
	$product_data['post_category'] = stripslashes_deep( $_POST['post_category'] ) ; 	
	$product_data['product_code_inmid'] = stripslashes_deep( $_POST['product_code_inmid'] ) ; 	
	if (isset($_FILES['post_image'])) {
			$fileData = $_FILES['post_image'];
		} else {
			$fileData = '';
	}	
	$form_validation_product = product_form_validation( $product_data , $fileData ); 
	$form_validation_interest = interest_form_validation( ); 	
	//print_r( $product_interest_validation_errors ); exit;
	if ( sizeof( $product_interest_validation_errors->get_error_messages() ) <= 0 )  { 
		echo "Not Error";
		/******************** Start : Product Insert HERE ***************/
		$product_data['post_category'] = array( $product_data['post_category'] ); 
		$product_data['post_type'] = 'product'; 
		$product_data['post_status']  = 'pending';
		$product_data['post_author']  = get_current_user_id();	
		$new_post_id = wp_insert_post( $product_data, $wp_error );
		if( $new_post_id ){
			$interest_insert = product_interest_insert( $new_post_id ); 
			wp_set_post_categories($new_post_id,  $product_data['post_category']  );
			if (!function_exists('media_handle_upload')) {
				require_once (ABSPATH . '/wp-admin/includes/media.php');
				require_once (ABSPATH . '/wp-admin/includes/file.php');
				require_once (ABSPATH . '/wp-admin/includes/image.php');
			}
			$attachmentIds = array();
			$imageCounter = 0;
			if ($fileData['name'] ) { 
				$imageInfo = @getimagesize($fileData['tmp_name']);
				$key = "public-submission-attachment-0";
				$_FILES[$key] = array();
				$_FILES[$key]['name']     = $fileData['name'];
				$_FILES[$key]['tmp_name'] = $fileData['tmp_name'];
				$_FILES[$key]['type']     = $fileData['type'];
				$_FILES[$key]['error']    = $fileData['error'];
				$_FILES[$key]['size']     = $fileData['size'];
				$attachmentId = media_handle_upload($key, $new_post_id );
				if (!is_wp_error($attachmentId) && wp_attachment_is_image($attachmentId)) {
					$attachmentIds[] = $attachmentId;
					add_post_meta($new_post_id, $product_post_meta_Image, wp_get_attachment_url($attachmentId));			   	   
					$imageCounter++;
				} else {
					wp_delete_attachment($attachmentId);
				}
			}
			$authorName = get_the_author_meta('display_name',  $product_data['post_author'] );
			if (isset($_SERVER['REMOTE_ADDR'])){
				$authorIp = sanitize_text_field($_SERVER['REMOTE_ADDR']);
			}  
			update_post_meta($new_post_id, $product_post_meta_IsSubmission, true);
			update_post_meta($new_post_id, $product_post_meta_Submitter,    sanitize_text_field($authorName));
			update_post_meta($new_post_id, $product_post_meta_SubmitterIp,  sanitize_text_field($authorIp));
			update_post_meta($new_post_id, 'product_code_inmid',  sanitize_text_field($product_data['product_code_inmid']));
		}
		/******************** End : Product Insert HERE ***************/
	}else{ //echo "Error";   
	}
}
	global $product_form; 
	$args = array( 'type' => 'product', 
							'orderby'                  => 'name',
							'order'                    => 'ASC',
							'hide_empty'               => 0,
							'taxonomy'                 => 'product_cat'
	);	
	$categories = get_categories( $args );
	$categories_options = array( array( 'label' =>__('Välj en kategori' ,TEXTDOMAIN ), 'value' => '' )); 
	if( $categories ){
		foreach( $categories as $categorie ){
			array_push( $categories_options, array( 'label' =>$categorie->cat_name, 'value' => $categorie->cat_ID  ) ); 
			/*if( isset( $_POST['post_category'] ) && in_array( $categorie->cat_ID , $_POST['post_category'] ) ){
				array_push( $categories_options, array( 'label' =>$categorie->cat_name, 'value' => $categorie->cat_ID, 'selected' => 'selected' ) );
			}else{ array_push( $categories_options, array( 'label' =>$categorie->cat_name, 'value' => $categorie->cat_ID , 'selected' => '' ) ); }*/
		}
	}
	$product_form = array( 
		array(	'label' => __('Beskriv Produkt eller Tjänst' ,TEXTDOMAIN ), 
					'name'=>'my_product_details', 
					'id'=>'my_product_details', 
					'type'=>'label'			
		),				
		array(	'label' => __('Produkt Namn * ' , TEXTDOMAIN  ), 
					'name'=>'post_title', 
					'id'=>'post_title', 
					'type'=>'text',
					'value' => $product_data['post_title'],
					'placeholder' =>__('Produkt Namn', TEXTDOMAIN )
		), 
	   	array(	'label' => __('Bild' , TEXTDOMAIN  ), 
					'name'=>'post_image', 
					'id'=>'post_image', 
					'type'=>'file',
					'value' => ''					
		) 			
	);		
	array_push( $product_form,array(	'label' => __('Kategori' , TEXTDOMAIN  ), 
															'name'=>'post_category', 
															'id'=>'post_category', 
															'type'=>'select',
															'options' => $categories_options 
												)  
				); 				
	$settings = array(
					    'wpautop'       => true,  // enable rich text editor
					    'media_buttons' => false,  // enable add media button
					    'textarea_name' => 'post_content', // name
					    'textarea_rows' => '10',  // number of textarea rows
					    'tabindex'      => '',    // tabindex
					    'editor_css'    => '',    // extra CSS
					    'editor_class'  => 'product-textarea', // class
					    'teeny'         => false, // output minimal editor config
					    'dfw'           => false, // replace fullscreen with DFW
					    'tinymce'       => true,  // enable TinyMCE
					    'quicktags'     => true,  // enable quicktags
					);
	array_push( $product_form,array(	'label' => __('Beskrivning' , TEXTDOMAIN  ), 
															'name'=>'post_content', 
															'id'=>'post_content', 
															'type'=>'texteditor',
															'value' => $product_data['post_content'],
															'settings' => $settings	
												)
				);
	array_push( $product_form,array(	'label' => __('Produktkod' , TEXTDOMAIN  ), 
												'name'=>'product_code_inmid', 
												'id'=>'product_code_inmid', 
												'type'=>'text',
												'value' => $product_data['product_code_inmid'],
												"tooltip_label" => 'förklaring:  modellbeteckning som tillverkaren använder för att identifiera produkten eller tjänsten'	
									)
	);
	get_template_part('views/add', 'my-product-interest-inmid');	
}


/*********  Check Product Image Right Size **********/
/** Author: ABU TAHER, Logic-coder IT
 *
* return $widthFits && $heightFits OR Empty if not matched with proper size
 */
function product_imageIsRightSize($width, $height) {
	global $product_options; 
	$widthFits = ($width <= intval($product_options['max-image-width'])) && ($width >= $product_options['min-image-width']);
	$heightFits = ($height <= $product_options['max-image-height']) && ($height >= $product_options['min-image-height']);
	return $widthFits && $heightFits;
}
/*********  Show interest form for Add My Interest **********/
/** Author: ABU TAHER, Logic-coder IT
 * Display interest form for Add My Interest
 *
* return interest form for Add My Interest
 */
		
add_action('inmid_product_interest_form_add_my_interest', 'inmid_add_my_interest_form');
function inmid_add_my_interest_form(){ 	
	get_template_part('woocommerce/single-product/product', 'attributes_add_my_interest_inmid');	
}

/*********  Add My Interest : Form Validation **********/
/** Author: ABU TAHER, Logic-coder IT
 * return Success/Failure Message
 */
 
add_action('inmid_product_interest_form_add_my_interest_form_validation', 'inmid_add_my_interest_form_validation');
function inmid_add_my_interest_form_validation(){ 	
	get_template_part('woocommerce/single-product/product', 'attributes_add_my_interest_form_validation_inmid');	
}

/*********  Add My Interest : Insert interest **********/
/** Author: ABU TAHER, Logic-coder IT
 * return Success/Failure Message
 */ 
add_action('inmid_product_interest_form_add_my_interest_insert', 'inmid_add_my_interest_insert', 10, 2 );
function inmid_add_my_interest_insert( $product_id, $product_name ){ 	
	global $wp_interest_form_data;
	$wp_interest_form_data['product_id'] = $product_id; 
	$wp_interest_form_data['product_name'] = $product_name; 
	get_template_part('woocommerce/single-product/product', 'attributes_add_my_interest_insert_inmid');	
}

/*********  Show Product Status : CIQ **********/
/** Author: ABU TAHER, Logic-coder IT
 * return CIQ Status Message
 */ 
add_action('inmid_product_status', 'inmid_product_status_CIQ' , 5 );
function inmid_product_status_CIQ( ){ 	
	get_template_part('woocommerce/single-product/product', 'status_ciq_inmid');	
}

/*********  Show Product Status : PPQ **********/
/** Author: ABU TAHER, Logic-coder IT
 * return PPQ Status Message
 */ 
add_action('inmid_product_status', 'inmid_product_status_PPQ' , 10 );
function inmid_product_status_PPQ( ){ 	
	get_template_part('woocommerce/single-product/product', 'status_ppq_inmid');	
}

/*********  Show Product Status : Status = Under Evaluation / MMQ Reached **********/
/** Author: ABU TAHER, Logic-coder IT
 * return Status = Under Evaluation / MMQ Reached  Message
 */ 
add_action('inmid_product_status', 'inmid_product_status_MMQ' , 15 );
function inmid_product_status_MMQ( ){ 	
	get_template_part('woocommerce/single-product/product', 'status_mmq_inmid');	
}
/*********  Show Product Status : CMP **********/
/** Author: ABU TAHER, Logic-coder IT
 * return CMP  Message
 */ 
add_action('inmid_product_status_cmp', 'inmid_product_status_cmp',20  );
function inmid_product_status_cmp( ){ 	
	get_template_part('woocommerce/single-product/product', 'status_cmp_inmid');	
}
/*********  Show Product Status : CMP Button **********/
/** Author: ABU TAHER, Logic-coder IT
 * return CMP Button 
 */ 
add_action('inmid_product_status_cmp_button', 'inmid_product_status_cmp_btn'  );
function inmid_product_status_cmp_btn( ){ 	
	get_template_part('woocommerce/single-product/product', 'status_cmp_btn_inmid');	
}

/** Author: ABU TAHER, Logic-coder IT
 * get_product_ppq 
 *
 * @param $product_id,  $flag= "in_general" means Check a group price set or not for a particular product  
 * @param $flag= "for_interest" means Check a group price set or not for a particular interest  
 * @param $product_interest_id , When $flag= "in_general" $product_interest_id will be empty
 * return Query Result
 */
function get_product_ppq( $product_id, $product_interest_id, $flag ){ 
global $wpdb;
if( $flag== "in_general" ){
	$results = $wpdb->get_results( "SELECT SQL_CALC_FOUND_ROWS * ,  sum(	interest_qty ) as total_qty FROM wp_product_interest
					JOIN wp_interest_group_price ON wp_product_interest.interest_group_id = 				
					wp_interest_group_price.group_id WHERE wp_product_interest.product_id='".	
					$product_id."' AND wp_product_interest.interest_campaign_closed=0", OBJECT );
}elseif( $flag== "for_interest" ){ 
	$results = $wpdb->get_results( "SELECT SQL_CALC_FOUND_ROWS * ,  sum(	interest_qty ) as total_qty FROM wp_product_interest
					JOIN wp_interest_group_price ON wp_product_interest.interest_group_id = 				
					wp_interest_group_price.group_id WHERE wp_product_interest.product_interest_id='".	
					$product_interest_id."' AND wp_product_interest.user_id='".get_current_user_id()."' AND wp_product_interest.interest_campaign_closed=0", OBJECT );
}
return $results;
}
/** Author: ABU TAHER, Logic-coder IT
 * inmid_theme_custom_scripts 
 *
 */
function inmid_theme_custom_scripts() {
	if ( ! is_admin() ) {				
		wp_enqueue_script('jquery-ui-datepicker');
		wp_enqueue_style('jquery-style', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');
		wp_enqueue_script('jquery'); 
		wp_register_script('addons_script', CODEDROP_DIR . '/init/js/jquery.tools.min.js', '');
		wp_enqueue_script('addons_script');		
		wp_register_script('inmid-scripts',  CODEDROP_DIR . '/init/js/inmid.js', '');
		wp_register_script('inmid-scripts',  CODEDROP_DIR . '/init/js/inmid.js', '');
		wp_enqueue_script( 'inmid-scripts');
		wp_enqueue_style( 'inmid-style', CODEDROP_DIR . '/inmid.css' );
	}
}
add_action( 'wp_enqueue_scripts', 'inmid_theme_custom_scripts' ); 
