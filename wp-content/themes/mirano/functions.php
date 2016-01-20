<?php
/**
 * Mirano Theme functions and definitions
 */

/**
* Require files
*/
	//TGM-Plugin-Activation
require_once get_template_directory(). '/class-tgm-plugin-activation.php';
	//Init the Redux Framework
if ( class_exists( 'ReduxFramework' ) && !isset( $redux_demo ) && file_exists( get_template_directory().'/theme-config.php' ) ) {
	require_once( get_template_directory().'/theme-config.php' );
}
	// Theme files
if ( !class_exists( 'road_widgets' ) && file_exists( get_template_directory().'/include/roadwidgets.php' ) ) {
	require_once( get_template_directory().'/include/roadwidgets.php' );
}
if ( file_exists( get_template_directory().'/include/styleswitcher.php' ) ) {
	require_once( get_template_directory().'/include/styleswitcher.php' );
}
if ( file_exists( get_template_directory().'/include/wooajax.php' ) ) {
	require_once( get_template_directory().'/include/wooajax.php' );
}
if ( file_exists( get_template_directory().'/include/shortcodes.php' ) ) {
	require_once( get_template_directory().'/include/shortcodes.php' );
}
Class Mirano_Theme {
	/**
	* Constructor
	*/
	function __construct() {
		// Register action/filter callbacks
		
			//WooCommerce - action/filter
		add_theme_support( 'woocommerce' );
		remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10);
		remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10);
		add_filter( 'get_product_search_form', array($this, 'road_woo_search_form'));
		add_filter( 'woocommerce_shortcode_products_query', array($this, 'road_woocommerce_shortcode_limit') );
		add_action( 'woocommerce_share', array($this, 'road_woocommerce_social_share'), 35 );
		add_action( 'woocommerce_archive_description', array($this, 'road_woocommerce_category_image'), 2 );
		
			//move message to top
		remove_action( 'woocommerce_before_shop_loop', 'wc_print_notices', 10 );
		add_action( 'woocommerce_show_message', 'wc_print_notices', 10 );

			//remove cart total under cross sell
		remove_action( 'woocommerce_cart_collaterals', 'woocommerce_cart_totals', 10 );

			//Single product organize
		add_action( 'woocommerce_single_product_summary', array($this, 'road_product_stock_status'), 15 );
		remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );
		add_action( 'woocommerce_show_related_products', 'woocommerce_output_related_products', 20 );

		//bestbuy-bestsell Customization
		remove_action( 'woocommerce_single_product_summary', array($this, 'road_product_stock_status'), 15 );

		//WooProjects - Project organize
		remove_action( 'projects_before_single_project_summary', 'projects_template_single_title', 10 );
		add_action( 'projects_single_project_summary', 'projects_template_single_title', 5 );
		remove_action( 'projects_before_single_project_summary', 'projects_template_single_short_description', 20 );
		remove_action( 'projects_before_single_project_summary', 'projects_template_single_gallery', 40 );
		add_action( 'projects_single_project_gallery', 'projects_template_single_gallery', 40 );
		
			//WooProjects - projects list
		remove_action( 'projects_loop_item', 'projects_template_loop_project_title', 20 );
		
			//Theme actions
		add_action( 'after_setup_theme', array($this, 'road_setup'));
		add_action( 'tgmpa_register', array($this, 'road_register_required_plugins')); 
		add_action( 'widgets_init', array($this, 'road_override_woocommerce_widgets'), 15 );
		
		add_action( 'wp_enqueue_scripts', array($this, 'road_scripts_styles') );
		add_action( 'wp_head', array($this, 'road_custom_code_header'));
		add_action( 'widgets_init', array($this, 'road_widgets_init'));
		add_action( 'add_meta_boxes', array($this, 'road_add_meta_box'));
		add_action( 'save_post', array($this, 'road_save_meta_box_data'));
		add_action('comment_form_before_fields', array($this, 'road_before_comment_fields'));
		add_action('comment_form_after_fields', array($this, 'road_after_comment_fields'));
		add_action( 'customize_register', array($this, 'road_customize_register'));
		add_action( 'customize_preview_init', array($this, 'road_customize_preview_js'));
		add_action( 'admin_head', array($this, 'road_remove_redux_ads'));
		
			//Theme filters
		add_filter( 'woocommerce_get_price_html', array($this, 'road_woo_price_html'), 100, 2 );
		add_filter( 'loop_shop_per_page', array($this, 'road_woo_change_per_page'), 20 );
		add_filter( 'woocommerce_output_related_products_args', array($this, 'road_woo_related_products_limit'));
		add_filter( 'get_search_form', array($this, 'road_search_form'));
		add_filter('excerpt_more', array($this, 'road_new_excerpt_more'));
		add_filter( 'excerpt_length', array($this, 'road_change_excerpt_length'), 999 );
		add_filter('wp_nav_menu_objects', array($this, 'road_first_and_last_menu_class'));
		add_filter( 'wp_page_menu_args', array($this, 'road_page_menu_args'));
		add_filter('dynamic_sidebar_params', array($this, 'road_widget_first_last_class'));
		add_filter('dynamic_sidebar_params', array($this, 'road_mega_menu_widget_change'));
		add_filter( 'dynamic_sidebar_params', array($this, 'road_put_widget_content'));
		
		//Adding theme support
		if ( ! isset( $content_width ) ) {
			$content_width = 625;
		}
	}
	/**
	* Filter callbacks
	* ----------------
	*/
	//Change woocommerce search form
	function road_woo_search_form( $form ) {
		global $wpdb;
		
		if(get_search_query()!=''){
			$search_str = get_search_query();
		} else {
			$search_str = esc_html__( 'Search product...', 'mirano' );
		}
		
		$form = '<form role="search" method="get" id="searchform" action="'.esc_url( home_url( '/'  ) ).'">';
			$form .= '<div>';
				$form .= '<input type="text" value="'.esc_attr($search_str).'" name="s" id="ws" placeholder="" />';
				$form .= '<button class="btn btn-primary" type="submit" id="wsearchsubmit"><i class="fa fa-search"></i></button>';
				$form .= '<input type="hidden" name="post_type" value="product" />';
			$form .= '</div>';
		$form .= '</form>';
		$form .= '<script type="text/javascript">';
		$form .= 'jQuery(document).ready(function(){
			jQuery("#ws").focus(function(){
				if(jQuery(this).val()=="'.esc_html__( 'Search product...', 'mirano' ).'"){
					jQuery(this).val("");
				}
			});
			jQuery("#ws").focusout(function(){
				if(jQuery(this).val()==""){
					jQuery(this).val("'.esc_html__( 'Search product...', 'mirano' ).'");
				}
			});
			jQuery("#wsearchsubmit").click(function(){
				if(jQuery("#ws").val()=="'.esc_html__( 'Search product...', 'mirano' ).'" || jQuery("#ws").val()==""){
					jQuery("#ws").focus();
					return false;
				}
			});
		});';
		$form .= '</script>';
		return $form;
	}
	//Limit number of products by shortcode [products]
	function road_woocommerce_shortcode_limit( $args ) {
		global $road_opt, $road_productsfound;
		
		if(isset($road_opt['shortcode_limit']) && $args['posts_per_page']==-1) {
			$args['posts_per_page'] = $road_opt['shortcode_limit'];
		}
		
		$road_productsfound = new WP_Query($args);
		$road_productsfound = $road_productsfound->post_count;
		
		return $args;
	}
	//Change price html
	function road_woo_price_html( $price, $product ){

		if($product->product_type=="variable") {
			if($product->get_variation_sale_price() && $product->get_variation_regular_price()!=$product->get_variation_sale_price()){
				$rprice = $product->get_variation_regular_price();
				$sprice = $product->get_variation_sale_price();
				
				return '<span class="special-price">'.( ( is_numeric( $sprice ) ) ? woocommerce_price( $sprice ) : $sprice ) .'</span><span class="old-price">'. ( ( is_numeric( $rprice ) ) ? woocommerce_price( $rprice ) : $rprice ) .'</span>'.$product->get_price_suffix();
			} else {
				$rprice = $product->get_variation_regular_price();
				return '<span class="special-price">' . ( ( is_numeric( $rprice ) ) ? woocommerce_price( $rprice ) : $rprice ) . '</span>'.$product->get_price_suffix();
			}
		}
		if ( $product->price > 0 ) {
			if ( $product->price && isset( $product->regular_price ) && ( $product->price!=$product->regular_price )) {
			$rprice = $product->regular_price;
			$sprice = $product->price;
			return '<span class="special-price">'.( ( is_numeric( $sprice ) ) ? woocommerce_price( $sprice ) : $sprice ) .'</span><span class="old-price">'. ( ( is_numeric( $rprice ) ) ? woocommerce_price( $rprice ) : $rprice ) .'</span>'.$product->get_price_suffix();
			} else {
			$sprice = $product->price;
			return '<span class="special-price">' . ( ( is_numeric( $sprice ) ) ? woocommerce_price( $sprice ) : $sprice ) . '</span>'.$product->get_price_suffix();
			}
		} else {
			return '<span class="special-price">0</span>'.$product->get_price_suffix();
		}
	}
	// Change products per page
	function road_woo_change_per_page() {
		global $road_opt;
		
		return $road_opt['product_per_page'];
	}
	//Change number of related products on product page. Set your own value for 'posts_per_page'
	function road_woo_related_products_limit( $args ) {
		global $product, $road_opt;
		$args['posts_per_page'] = $road_opt['related_amount'];

		return $args;
	}
	//Change search form
	function road_search_form( $form ) {
		if(get_search_query()!=''){
			$search_str = get_search_query();
		} else {
			$search_str = esc_html__( 'Search...', 'mirano' );
		}
		
		$form = '<form role="search" method="get" id="blogsearchform" class="searchform" action="' . esc_url(home_url( '/' ) ). '" >
		<div class="form-input">
			<input class="input_text" type="text" value="'.esc_attr($search_str).'" name="s" id="search_input" />
			<button class="button" type="submit" id="blogsearchsubmit"><i class="fa fa-search"></i></button>
			<input type="hidden" name="post_type" value="post" />
			</div>
		</form>';
		$form .= '<script type="text/javascript">';
		$form .= 'jQuery(document).ready(function(){
			jQuery("#search_input").focus(function(){
				if(jQuery(this).val()=="'.esc_html__( 'Search...', 'mirano' ).'"){
					jQuery(this).val("");
				}
			});
			jQuery("#search_input").focusout(function(){
				if(jQuery(this).val()==""){
					jQuery(this).val("'.esc_html__( 'Search...', 'mirano' ).'");
				}
			});
			jQuery("#blogsearchsubmit").click(function(){
				if(jQuery("#search_input").val()=="'.esc_html__( 'Search...', 'mirano' ).'" || jQuery("#search_input").val()==""){
					jQuery("#search_input").focus();
					return false;
				}
			});
		});';
		$form .= '</script>';
		return $form;
	}

	// Replaces the excerpt "more" text by a link
	function road_new_excerpt_more($more) {
		return '';
	}

	//Change excerpt length
	function road_change_excerpt_length( $length ) {
		global $road_opt;
		
		if(isset($road_opt['excerpt_length'])){
			return $road_opt['excerpt_length'];
		}
		
		return 22;
	}

	//Add 'first, last' class to menu
	function road_first_and_last_menu_class($items) {
		$items[1]->classes[] = 'first';
		$items[count($items)]->classes[] = 'last';
		return $items;
	}

	//Add first, last class to widgets
	function road_widget_first_last_class($params) {
		global $my_widget_num;
		
		$class = '';
		
		$this_id = $params[0]['id']; // Get the id for the current sidebar we're processing
		$arr_registered_widgets = wp_get_sidebars_widgets(); // Get an array of ALL registered widgets	

		if(!$my_widget_num) {// If the counter array doesn't exist, create it
			$my_widget_num = array();
		}

		if(!isset($arr_registered_widgets[$this_id]) || !is_array($arr_registered_widgets[$this_id])) { // Check if the current sidebar has no widgets
			return $params; // No widgets in this sidebar... bail early.
		}

		if(isset($my_widget_num[$this_id])) { // See if the counter array has an entry for this sidebar
			$my_widget_num[$this_id] ++;
		} else { // If not, create it starting with 1
			$my_widget_num[$this_id] = 1;
		}

		if($my_widget_num[$this_id] == 1) { // If this is the first widget
			$class .= ' widget-first ';
		} elseif($my_widget_num[$this_id] == count($arr_registered_widgets[$this_id])) { // If this is the last widget
			$class .= ' widget-last ';
		}
		
		$params[0]['before_widget'] = str_replace('first_last', ' '.$class.' ', $params[0]['before_widget']);
		
		return $params;
	}

	//Change mega menu widget from div to li tag
	function road_mega_menu_widget_change($params) {
		
		$sidebar_id = $params[0]['id'];
		
		$pos = strpos($sidebar_id, '_menu_widgets_area_');
		
		if ( !$pos == false ) {
			$params[0]['before_widget'] = '<li class="widget_menu">'.$params[0]['before_widget'];
			$params[0]['after_widget'] = $params[0]['after_widget'].'</li>';
		}
		
		return $params;
	}
	/**
	 * Filter the page menu arguments.
	 *
	 * Makes our wp_nav_menu() fallback -- wp_page_menu() -- show a home link.
	 *
	 * @since Road Themes 1.0
	 */
	function road_page_menu_args( $args ) {
		if ( ! isset( $args['show_home'] ) )
			$args['show_home'] = true;
		return $args;
	}
	// Push sidebar widget content into a div
	function road_put_widget_content( $params ) {
		global $wp_registered_widgets;

		if( $params[0]['id']=='sidebar-category' ){
			$settings_getter = $wp_registered_widgets[ $params[0]['widget_id'] ]['callback'][0];
			$settings = $settings_getter->get_settings();
			$settings = $settings[ $params[1]['number'] ];
			
			if($params[0]['widget_name']=="Text" && isset($settings['title']) && $settings['text']=="") { // if text widget and no content => don't push content
				return $params;
			}
			if( isset($settings['title']) && $settings['title']!='' ){
				$params[0][ 'after_title' ] .= '<div class="widget_content">';
				$params[0][ 'after_widget' ] = '</div>'.$params[0][ 'after_widget' ];
			} else {
				$params[0][ 'before_widget' ] .= '<div class="widget_content">';
				$params[0][ 'after_widget' ] = '</div>'.$params[0][ 'after_widget' ];
			}
		}
		
		return $params;
	}
	/**
	* Action hooks
	* ----------------
	*/
	//Override woocommerce widgets
	function road_override_woocommerce_widgets() {
		//Show mini cart on all pages
		if ( class_exists( 'WC_Widget_Cart' ) ) {
			unregister_widget( 'WC_Widget_Cart' ); 
			include_once( 'woocommerce/class-wc-widget-cart.php' );
			register_widget( 'Custom_WC_Widget_Cart' );
		}
	}
	//Display social sharing on product page
	function road_woocommerce_social_share(){
		global $road_opt;
	?>
		<div class="share_buttons">
			<?php if ($road_opt['share_code']!='') {
				echo wp_kses($road_opt['share_code'], array(
					'div' => array(
						'class' => array()
					),
					'span' => array(
						'class' => array(),
						'displayText' => array()
					),
				));
			} ?>
		</div>
	<?php
	}
	// Add image to category description
	function road_woocommerce_category_image() {
		if ( is_product_category() ){
			global $wp_query;
			
			$cat = $wp_query->get_queried_object();
			$thumbnail_id = get_woocommerce_term_meta( $cat->term_id, 'thumbnail_id', true );
			$image = wp_get_attachment_url( $thumbnail_id );
			
			if ( $image ) {
				echo '<p class="category-image-desc"><img src="' . esc_url($image) . '" alt="" /></p>';
			}
		}
	}
	//Display stock status on product page
	function road_product_stock_status(){
		global $product;
		?>
		<div class="stock-status">
			<?php if($product->is_in_stock()){ ?>
				<p><?php esc_html_e('In stock', 'mirano');?></p>
			<?php } else { ?>
				<p><?php esc_html_e('Out of stock', 'mirano');?></p>
			<?php } ?>
		</div>
		<?php
	}
	/**
	 * Road Themes setup.
	 *
	 * Sets up theme defaults and registers the various WordPress features that
	 * Road Themes supports.
	 *
	 * @uses load_theme_textdomain() For translation/localization support.
	 * @uses add_editor_style() To add a Visual Editor stylesheet.
	 * @uses add_theme_support() To add support for post thumbnails, automatic feed links,
	 * 	custom background, and post formats.
	 * @uses register_nav_menu() To add support for navigation menus.
	 * @uses set_post_thumbnail_size() To set a custom post thumbnail size.
	 *
	 * @since Road Themes 1.0
	 */
	function road_setup() {
		/*
		 * Makes Road Themes available for translation.
		 *
		 * Translations can be added to the /languages/ directory.
		 * If you're building a theme based on Road Themes, use a find and replace
		 * to change 'mirano' to the name of your theme in all the template files.
		 */
		load_theme_textdomain( 'mirano', get_template_directory() . '/languages' );

		// This theme styles the visual editor with editor-style.css to match the theme style.
		add_editor_style();

		// Adds RSS feed links to <head> for posts and comments.
		add_theme_support( 'automatic-feed-links' );

		// This theme supports a variety of post formats.
		add_theme_support( 'post-formats', array( 'image', 'gallery', 'video', 'audio' ) );

		// Register menus
		register_nav_menu( 'primary', esc_html__( 'Primary Menu', 'mirano' ) );
		register_nav_menu( 'mobilemenu', esc_html__( 'Mobile Menu', 'mirano' ) );

		/*
		 * This theme supports custom background color and image,
		 * and here we also set up the default background color.
		 */
		add_theme_support( 'custom-background', array(
			'default-color' => 'e6e6e6',
		) );

		/*
		 * Let WordPress manage the document title.
		 * By adding theme support, we declare that this theme does not use a
		 * hard-coded <title> tag in the document head, and expect WordPress to
		 * provide it for us.
		 */
		add_theme_support( 'title-tag' );

		// This theme uses a custom image size for featured images, displayed on "standard" posts.
		add_theme_support( 'post-thumbnails' );

		set_post_thumbnail_size( 1170, 9999 ); // Unlimited height, soft crop
		add_image_size( 'mirano-post-thumb', 400, 400, true ); // (cropped)
		add_image_size( 'mirano-post-thumbwide', 770, 460, true ); // (cropped)
	}
	/**
	 * Enqueue scripts and styles for front-end.
	 *
	 * @since Road Themes 1.0
	 */
	function road_scripts_styles() {
		global $wp_styles, $wp_scripts, $road_opt;
		
		/*
		 * Adds JavaScript to pages with the comment form to support
		 * sites with threaded comments (when in use).
		*/
		
		if ( is_singular() && comments_open() && get_option( 'thread_comments' ) )
			wp_enqueue_script( 'comment-reply' );
		
		// Add Bootstrap JavaScript
		wp_enqueue_script( 'bootstrap-js', get_template_directory_uri() . '/js/bootstrap.min.js', array('jquery'), '3.2.0', true );
		
		// Add Slick files
		wp_enqueue_script( 'slick-js', get_template_directory_uri() . '/js/slick/slick.min.js', array('jquery'), '1.3.15', true );
		wp_enqueue_style( 'slick-style', get_template_directory_uri() . '/js/slick/slick.css', array(), '1.3.15' );
		
		// Add Chosen js files
		wp_enqueue_script( 'chosen-js', get_template_directory_uri() . '/js/chosen/chosen.jquery.min.js', array('jquery'), '1.3.0', true );
		wp_enqueue_script( 'chosenproto-js', get_template_directory_uri() . '/js/chosen/chosen.proto.min.js', array('jquery'), '1.3.0', true );
		wp_enqueue_style( 'chosen-style', get_template_directory_uri() . '/js/chosen/chosen.min.css', array(), '1.3.0' );
		
		// Add parallax script files
		
		// Add Fancybox
		wp_enqueue_script( 'fancybox', get_template_directory_uri() . '/js/fancybox/jquery.fancybox.pack.js', array('jquery'), '2.1.5', true );
		wp_enqueue_script( 'fancybox-buttons', get_template_directory_uri() . '/js/fancybox/helpers/jquery.fancybox-buttons.js', array('jquery'), '1.0.5', true );
		wp_enqueue_script( 'fancybox-media', get_template_directory_uri() . '/js/fancybox/helpers/jquery.fancybox-media.js', array('jquery'), '1.0.6', true );
		wp_enqueue_script( 'fancybox-thumbs', get_template_directory_uri() . '/js/fancybox/helpers/jquery.fancybox-thumbs.js', array('jquery'), '1.0.7', true );
		wp_enqueue_style( 'fancybox-css', get_template_directory_uri() . '/js/fancybox/jquery.fancybox.css', array(), '2.1.5' );
		wp_enqueue_style( 'fancybox-buttons', get_template_directory_uri() . '/js/fancybox/helpers/jquery.fancybox-buttons.css', array(), '1.0.5' );
		wp_enqueue_style( 'fancybox-thumbs', get_template_directory_uri() . '/js/fancybox/helpers/jquery.fancybox-thumbs.css', array(), '1.0.7' );
		
		//Superfish
		wp_enqueue_script( 'superfish-js', get_template_directory_uri() . '/js/superfish/superfish.min.js', array('jquery'), '1.3.15', true );
		
		//Add Twitter js
		wp_enqueue_script( 'twitter-js', 'http://platform.twitter.com/widgets.js', array('jquery'), '', true );
		
		//Add Shuffle js
		wp_enqueue_script( 'modernizr-js', get_template_directory_uri() . '/js/modernizr.custom.min.js', array('jquery'), '2.6.2', true );
		wp_enqueue_script( 'shuffle-js', get_template_directory_uri() . '/js/jquery.shuffle.min.js', array('jquery'), '3.0.0', true );

		//Add mousewheel
		wp_enqueue_script( 'mousewheel-js', get_template_directory_uri() . '/js/jquery.mousewheel.min.js', array('jquery'), '3.1.12', true );
		
		// Add jQuery countdown file
		wp_enqueue_script( 'countdown-js', get_template_directory_uri() . '/js/jquery.countdown.min.js', array('jquery'), '2.0.4', true );
		
		//Loads HTML5 JavaScript file to add support for HTML5 elements in older IE versions.
		wp_enqueue_script( 'html5-js', get_template_directory_uri() . '/js/html5.js', array(), '3.7.0', true );
		$wp_scripts->add_data( 'html5-js', 'conditional', 'lt IE 9' );
			
		// Add theme.js file
		wp_enqueue_script( 'theme-js', get_template_directory_uri() . '/js/theme.js', array('jquery'), '20140826', true );

		wp_enqueue_script( 'collapse-js', get_template_directory_uri() . '/js/jquery.ntm.js', array('jquery'), '20140826', true );
		wp_enqueue_style( 'collapse-css', get_template_directory_uri() . '/css/collapse.css', array('roadthemes-css'), '1.0.0' );

		$font_url = $this->road_get_font_url();
		if ( ! empty( $font_url ) )
			wp_enqueue_style( 'roadthemes-fonts', esc_url_raw( $font_url ), array(), null );
		
		// Loads our main stylesheet.
		wp_enqueue_style( 'roadthemes-style', get_stylesheet_uri() );

		// Mega Main Menu
		wp_enqueue_style( 'megamenu-css', get_template_directory_uri() . '/css/megamenu_style.css', array(), '2.0.4' );
		
		// Load fontawesome css
		wp_enqueue_style( 'fontawesome-css', get_template_directory_uri() . '/css/font-awesome.min.css', array(), '4.2.0' );
		
		// Load bootstrap css
		wp_enqueue_style( 'bootstrap-css', get_template_directory_uri() . '/css/bootstrap.min.css', array(), '3.2.0' );
		
		// Compile Less to CSS
		$previewpreset = (isset($_REQUEST['preset']) ? $_REQUEST['preset'] : null);
			//get preset from url (only for demo/preview)
		if($previewpreset){
			$_SESSION["preset"] = $previewpreset;
		}
		if(!isset($_SESSION["preset"])){
			$_SESSION["preset"] = 1;
		}
		if($_SESSION["preset"] != 1) {
			$presetopt = $_SESSION["preset"];
		} else { /* if no preset varialbe found in url, use from theme options */
			$presetopt = $road_opt['preset_option'];
		}
		if(!isset($presetopt)) $presetopt = 1; /* in case first time install theme, no options found */
		
		if($road_opt['enable_less']){
			$themevariables = array(
				'heading_font'=> $road_opt['headingfont']['font-family'],
				'menu_font'=> $road_opt['menufont']['font-family'],
				'body_font'=> $road_opt['bodyfont']['font-family'],
				'price_font'=> $road_opt['pricefont']['font-family'],
				'heading_color'=> $road_opt['headingfont']['color'],
				'menu_color'=> $road_opt['menufont']['color'],
				'text_color'=> $road_opt['bodyfont']['color'],
				'primary_color' => $road_opt['primary_color'],
				'sale_color' => $road_opt['sale_color'],
				'saletext_color' => $road_opt['saletext_color'],
				'rate_color' => $road_opt['rate_color'],
			);
			switch ($presetopt) {
				case 2:
					$themevariables['primary_color'] = '#c49654';
				break;
				
				case 3:
					$themevariables['primary_color'] = '#00B900';
				break;
				
				case 4:
					$themevariables['primary_color'] = '#FF8787';
				break;
				case 5:
					$themevariables['primary_color'] = '#EF7C3D';
				break;
				case 6:
					$themevariables['primary_color'] = '#DE4C4A';
				break;
			}
			if(function_exists('compileLessFile')){
				compileLessFile('theme.less', 'theme'.$presetopt.'.css', $themevariables);
				compileLessFile('ie.less', 'ie'.$presetopt.'.css', $themevariables);
			}
		}
		
		// Load main theme css style
		wp_enqueue_style( 'roadthemes-css', get_template_directory_uri() . '/css/theme'.$presetopt.'.css', array('bootstrap-css'), '1.0.0' );
		// Loads the Internet Explorer specific stylesheet.
		wp_enqueue_style( 'roadthemes-ie', get_template_directory_uri() . '/css/ie'.$presetopt.'.css', array( 'roadthemes-style' ), '20121010' );
		$wp_styles->add_data( 'roadthemes-ie', 'conditional', 'lte IE 9' );
		
		if($road_opt['enable_sswitcher']){
		// Add styleswitcher.js file
		wp_enqueue_script( 'styleswitcher-js', get_template_directory_uri() . '/js/styleswitcher.js', array(), '20140826', false );
		// Load styleswitcher css style
		wp_enqueue_style( 'styleswitcher-css', get_template_directory_uri() . '/css/styleswitcher.css', array(), '1.0.0' );
		// Load scroll bar js
		wp_enqueue_script( 'scrollbar-js', get_template_directory_uri() . '/js/jquery.scrollbar.min.js', array('jquery'), '0.2.8', true );
		wp_enqueue_style( 'scrollbar-css', get_template_directory_uri() . '/css/scrollbar.css', array(), '1.0.0' );
		}
	}
	//add custom css, javascript variables, sharing code to header
	function road_custom_code_header() {
		global $road_opt;

		if ( isset($road_opt['share_head_code']) && $road_opt['share_head_code']!='') {
			echo wp_kses($road_opt['share_head_code'], array(
				'script' => array(
					'type' => array(),
					'src' => array(),
					'async' => array()
				),
			));
		}
		if ( isset($road_opt['custom_css']) && $road_opt['custom_css']!='') { ?>
			<style><?php echo esc_html($road_opt['custom_css']); ?></style>
		<?php } ?>
			<script type="text/javascript">
			var road_brandnumber = <?php if(isset($road_opt['brandnumber'])) { echo esc_js($road_opt['brandnumber']); } else { echo '6'; } ?>,
				road_brandscroll = <?php echo esc_js($road_opt['brandscroll'])==1 ? 'true': 'false'; ?>,
				road_brandscrollnumber = <?php if(isset($road_opt['brandscrollnumber'])) { echo esc_js($road_opt['brandscrollnumber']); } else { echo '2';} ?>,
				road_brandpause = <?php if(isset($road_opt['brandpause'])) { echo esc_js($road_opt['brandpause']); } else { echo '3000'; } ?>,
				road_brandanimate = <?php if(isset($road_opt['brandanimate'])) { echo esc_js($road_opt['brandanimate']); } else { echo '700';} ?>;
			var road_blogscroll = <?php echo esc_js($road_opt['blogscroll'])==1 ? 'true': 'false'; ?>,
				road_blogpause = <?php if(isset($road_opt['blogpause'])) { echo esc_js($road_opt['blogpause']); } else { echo '3000'; } ?>,
				road_bloganimate = <?php if(isset($road_opt['bloganimate'])) { echo esc_js($road_opt['bloganimate']); } else { echo '700'; } ?>;
			var road_menu_number = <?php if(isset($road_opt['categories_menu_items'])) { echo esc_js((int)$road_opt['categories_menu_items']+1); } else { echo '9';} ?>;
			</script>
		<?php 
	}
	/**
	 * Register sidebars.
	 *
	 * Registers our main widget area and the front page widget areas.
	 *
	 * @since Road Themes 1.0
	 */
	function road_widgets_init() {
		register_sidebar( array(
			'name' => esc_html__( 'Blog Sidebar', 'mirano' ),
			'id' => 'sidebar-1',
			'description' => esc_html__( 'Sidebar on blog page', 'mirano' ),
			'before_widget' => '<aside id="%1$s" class="widget %2$s">',
			'after_widget' => '</aside>',
			'before_title' => '<h3 class="widget-title"><span>',
			'after_title' => '</span></h3>',
		) );
		
		register_sidebar( array(
			'name' => esc_html__( 'Category Sidebar', 'mirano' ),
			'id' => 'sidebar-category',
			'description' => esc_html__( 'Sidebar on product category page', 'mirano' ),
			'before_widget' => '<aside id="%1$s" class="widget %2$s">',
			'after_widget' => '</aside>',
			'before_title' => '<h3 class="widget-title"><span>',
			'after_title' => '</span></h3>',
		) );
		
		register_sidebar( array(
			'name' => esc_html__( 'Product Sidebar', 'mirano' ),
			'id' => 'sidebar-product',
			'description' => esc_html__( 'Sidebar on product details page', 'mirano' ),
			'before_widget' => '<aside id="%1$s" class="widget %2$s first_last">',
			'after_widget' => '</aside>',
			'before_title' => '<h3 class="widget-title"><span>',
			'after_title' => '</span></h3>',
		) );

		register_sidebar( array(
			'name' => esc_html__( 'Pages Sidebar', 'mirano' ),
			'id' => 'sidebar-page',
			'description' => esc_html__( 'Sidebar on content pages', 'mirano' ),
			'before_widget' => '<aside id="%1$s" class="widget %2$s">',
			'after_widget' => '</aside>',
			'before_title' => '<h3 class="widget-title"><span>',
			'after_title' => '</span></h3>',
		) );
	}
	function road_add_meta_box() {

		$screens = array( 'post' );

		foreach ( $screens as $screen ) {

			add_meta_box(
				'road_post_intro_section',
				esc_html__( 'Post featured content', 'mirano' ),
				'Mirano_Theme::road_meta_box_callback',
				$screen
			);
		}
	}
	function road_meta_box_callback( $post ) {

		// Add an nonce field so we can check for it later.
		wp_nonce_field( 'road_meta_box', 'road_meta_box_nonce' );

		/*
		 * Use get_post_meta() to retrieve an existing value
		 * from the database and use the value for the form.
		 */
		$value = get_post_meta( $post->ID, '_road_meta_value_key', true );

		echo '<label for="road_post_intro">';
		esc_html_e( 'This content will be used to replace the featured image, use shortcode here', 'mirano' );
		echo '</label><br />';
		//echo '<textarea id="road_post_intro" name="road_post_intro" rows="5" cols="50" />' . esc_attr( $value ) . '</textarea>';
		wp_editor( $value, 'road_post_intro', $settings = array() );
		
		
	}

	function road_save_meta_box_data( $post_id ) {

		/*
		 * We need to verify this came from our screen and with proper authorization,
		 * because the save_post action can be triggered at other times.
		 */

		// Check if our nonce is set.
		if ( ! isset( $_POST['road_meta_box_nonce'] ) ) {
			return;
		}

		// Verify that the nonce is valid.
		if ( ! wp_verify_nonce( $_POST['road_meta_box_nonce'], 'road_meta_box' ) ) {
			return;
		}

		// If this is an autosave, our form has not been submitted, so we don't want to do anything.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		// Check the user's permissions.
		if ( isset( $_POST['post_type'] ) && 'page' == $_POST['post_type'] ) {

			if ( ! current_user_can( 'edit_page', $post_id ) ) {
				return;
			}

		} else {

			if ( ! current_user_can( 'edit_post', $post_id ) ) {
				return;
			}
		}

		/* OK, it's safe for us to save the data now. */
		
		// Make sure that it is set.
		if ( ! isset( $_POST['road_post_intro'] ) ) {
			return;
		}

		// Sanitize user input.
		$my_data = sanitize_text_field( $_POST['road_post_intro'] );

		// Update the meta field in the database.
		update_post_meta( $post_id, '_road_meta_value_key', $my_data );
	}
	//Change comment form
	function road_before_comment_fields() {
		echo '<div class="comment-input">';
	}

	function road_after_comment_fields() {
		echo '</div>';
	}
	/**
	 * Register postMessage support.
	 *
	 * Add postMessage support for site title and description for the Customizer.
	 *
	 * @since Road Themes 1.0
	 *
	 * @param WP_Customize_Manager $wp_customize Customizer object.
	 */
	function road_customize_register( $wp_customize ) {
		$wp_customize->get_setting( 'blogname' )->transport         = 'postMessage';
		$wp_customize->get_setting( 'blogdescription' )->transport  = 'postMessage';
		$wp_customize->get_setting( 'header_textcolor' )->transport = 'postMessage';
	}

	/**
	 * Enqueue Javascript postMessage handlers for the Customizer.
	 *
	 * Binds JS handlers to make the Customizer preview reload changes asynchronously.
	 *
	 * @since Road Themes 1.0
	 */
	function road_customize_preview_js() {
		wp_enqueue_script( 'roadthemes-customizer', get_template_directory_uri() . '/js/theme-customizer.js', array( 'customize-preview' ), '20130301', true );
	}
	// Remove Redux Ads
	function road_remove_redux_ads() { ?>
	<style type="text/css">
	.rAds {
		display: none !important;
	}
	</style>
	<?php
	}
	/**
	* Utility methods
	* ---------------
	*/
	//Add breadcrumbs
	static function road_breadcrumb() {
		global $post, $road_opt;
		
		$brseparator = '<span class="separator">></span>';
		if (!is_home()) {
			echo '<div class="breadcrumbs">';
			
			echo '<a href="';
			echo esc_url(home_url( '/' ));
			echo '">';
			echo 'Home';
			echo '</a>'.$brseparator;
			if (is_category() || is_single()) {
				the_category($brseparator);
				if (is_single()) {
					echo wp_kses($brseparator, array(
						'span'=>array(
							'class'=>array()
							)
						)
					);
					the_title();
				}
			} elseif (is_page()) {
				if($post->post_parent){
					$anc = get_post_ancestors( $post->ID );
					$title = get_the_title();
					foreach ( $anc as $ancestor ) {
						$output = '<a href="'.get_permalink($ancestor).'" title="'.get_the_title($ancestor).'">'.get_the_title($ancestor).'</a>'.$brseparator;
					}
					echo ''.$output;
					echo '<span title="'.$title.'"> '.$title.'</span>';
				} else {
					echo '<span> '.get_the_title().'</span>';
				}
			}
			elseif (is_tag()) {single_tag_title();}
			elseif (is_day()) {echo"<span>Archive for "; the_time('F jS, Y'); echo'</span>';}
			elseif (is_month()) {echo"<span>Archive for "; the_time('F, Y'); echo'</span>';}
			elseif (is_year()) {echo"<span>Archive for "; the_time('Y'); echo'</span>';}
			elseif (is_author()) {echo"<span>Author Archive"; echo'</span>';}
			elseif (isset($_GET['paged']) && !empty($_GET['paged'])) {echo "<span>Blog Archives"; echo'</span>';}
			elseif (is_search()) {echo"<span>Search Results"; echo'</span>';}
			
			echo '</div>';
		} else {
			echo '<div class="breadcrumbs">';
			
			echo '<a href="';
			echo esc_url(home_url( '/' ));
			echo '">';
			echo 'Home';
			echo '</a>'.$brseparator;
			
			if(isset($road_opt['blog_header_text']) && $road_opt['blog_header_text']!=""){
				echo esc_html($road_opt['blog_header_text']);
			} else {
				echo 'Blog';
			}
			
			echo '</div>';
		}
	}
	static function roadlimitStringByWord ($string, $maxlength, $suffix = '') {

		if(function_exists( 'mb_strlen' )) {
			// use multibyte functions by Iysov
			if(mb_strlen( $string )<=$maxlength) return $string;
			$string = mb_substr( $string, 0, $maxlength );
			$index = mb_strrpos( $string, ' ' );
			if($index === FALSE) {
				return $string;
			} else {
				return mb_substr( $string, 0, $index ).$suffix;
			}
		} else { // original code here
			if(strlen( $string )<=$maxlength) return $string;
			$string = substr( $string, 0, $maxlength );
			$index = strrpos( $string, ' ' );
			if($index === FALSE) {
				return $string;
			} else {
				return substr( $string, 0, $index ).$suffix;
			}
		}
	}

	/**
	 * Return the Google font stylesheet URL if available.
	 *
	 * The use of Open Sans by default is localized. For languages that use
	 * characters not supported by the font, the font can be disabled.
	 *
	 * @since Road Themes 1.2
	 *
	 * @return string Font stylesheet or empty string if disabled.
	 */
	static function road_get_font_url() {
		$font_url = '';
    
		/*
		Translators: If there are characters in your language that are not supported
		by chosen font(s), translate this to 'off'. Do not translate into your own language.
		 */
		if ( 'off' !== _x( 'on', 'Google font: on or off', 'studio' ) ) {
			$font_url = add_query_arg( 'family', urlencode( 'Open Sans:400,400italic,700italic,700&subset=latin,latin-ext' ), "//fonts.googleapis.com/css" );
		}
		return $font_url;
	}

	/**
	 * Displays navigation to next/previous pages when applicable.
	 *
	 * @since Road Themes 1.0
	 */
	static function road_content_nav( $html_id ) {
		global $wp_query;

		$html_id = esc_attr( $html_id );

		if ( $wp_query->max_num_pages > 1 ) : ?>
			<nav id="<?php echo es_attr($html_id); ?>" class="navigation" role="navigation">
				<h3 class="assistive-text"><?php esc_html_e( 'Post navigation', 'mirano' ); ?></h3>
				<div class="nav-previous"><?php next_posts_link( esc_html__( '<span class="meta-nav">&larr;</span> Older posts', 'mirano' ) ); ?></div>
				<div class="nav-next"><?php previous_posts_link( esc_html__( 'Newer posts <span class="meta-nav">&rarr;</span>', 'mirano' ) ); ?></div>
			</nav>
		<?php endif;
	}

	/* Pagination */
	static function road_pagination() {
		global $wp_query;

		$big = 999999999; // need an unlikely integer
		
		echo paginate_links( array(
			'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
			'format' => '?paged=%#%',
			'current' => max( 1, get_query_var('paged') ),
			'total' => $wp_query->max_num_pages,
			'prev_text'    => esc_html__('Previous', 'mirano'),
			'next_text'    =>esc_html__('Next', 'mirano'),
		) );
	}


	/**
	 * Template for comments and pingbacks.
	 *
	 * To override this walker in a child theme without modifying the comments template
	 * simply create your own road_comment(), and that function will be used instead.
	 *
	 * Used as a callback by wp_list_comments() for displaying the comments.
	 *
	 * @since Road Themes 1.0
	 */
	static function road_comment( $comment, $args, $depth ) {
		$GLOBALS['comment'] = $comment;
		switch ( $comment->comment_type ) :
			case 'pingback' :
			case 'trackback' :
			// Display trackbacks differently than normal comments.
		?>
		<li <?php comment_class(); ?> id="comment-<?php comment_ID(); ?>">
			<p><?php esc_html_e( 'Pingback:', 'mirano' ); ?> <?php comment_author_link(); ?> <?php edit_comment_link( esc_html__( '(Edit)', 'mirano' ), '<span class="edit-link">', '</span>' ); ?></p>
		<?php
				break;
			default :
			// Proceed with normal comments.
			global $post;
		?>
		<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
			<article id="comment-<?php comment_ID(); ?>" class="comment">
				<div class="comment-avatar">
					<?php echo get_avatar( $comment, 50 ); ?>
				</div>
				<div class="comment-info">
					<header class="comment-meta comment-author vcard">
						<?php
							
							printf( '<cite><b class="fn">%1$s</b> %2$s</cite>',
								get_comment_author_link(),
								// If current post author is also comment author, make it known visually.
								( $comment->user_id === $post->post_author ) ? '<span>' . esc_html__( 'Post author', 'mirano' ) . '</span>' : ''
							);
							printf( '<time datetime="%1$s">%2$s</time>',
								get_comment_time( 'c' ),
								/* translators: 1: date, 2: time */
								sprintf( esc_html__( '%1$s at %2$s', 'mirano' ), get_comment_date(), get_comment_time() )
							);
						?>
						<div class="reply">
							<?php comment_reply_link( array_merge( $args, array( 'reply_text' => esc_html__( 'Reply', 'mirano' ), 'after' => '', 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
						</div><!-- .reply -->
					</header><!-- .comment-meta -->
					<?php if ( '0' == $comment->comment_approved ) : ?>
						<p class="comment-awaiting-moderation"><?php esc_html_e( 'Your comment is awaiting moderation.', 'mirano' ); ?></p>
					<?php endif; ?>

					<section class="comment-content comment">
						<?php comment_text(); ?>
						<?php edit_comment_link( esc_html__( 'Edit', 'mirano' ), '<p class="edit-link">', '</p>' ); ?>
					</section><!-- .comment-content -->
				</div>
			</article><!-- #comment-## -->
		<?php
			break;
		endswitch; // end comment_type check
	}

	/**
	 * Set up post entry meta.
	 *
	 * Prints HTML with meta information for current post: categories, tags, permalink, author, and date.
	 *
	 * Create your own road_entry_meta() to override in a child theme.
	 *
	 * @since Road Themes 1.0
	 */
	static function road_entry_meta() {
		
		// Translators: used between list items, there is a space after the comma.

		$categories_list = get_the_category_list( esc_html__( ', ', 'mirano' ) );

		$author = sprintf( '<span class="author vcard"><a class="url fn n" href="%1$s" title="%2$s" rel="author">%3$s</a></span>',
			esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
			esc_attr( sprintf( esc_html__( 'View all posts by %s', 'mirano' ), get_the_author() ) ),
			get_the_author()
		);

		$tag_list = get_the_tag_list( '', esc_html__( ', ', 'mirano' ) );

		$num_comments = (int)get_comments_number();
		$write_comments = '';
		if ( comments_open() ) {
			if ( $num_comments == 0 ) {
				$comments = esc_html__('0 comments', 'mirano');
			} elseif ( $num_comments > 1 ) {
				$comments = $num_comments . esc_html__(' comments', 'mirano');
			} else {
				$comments = esc_html__('1 comment', 'mirano');
			}
			$write_comments = '<a href="' . get_comments_link() .'">'. $comments.'</a>';
		}

		$utility_text = wp_kses(__( 'Posted by %1$s / %2$s / %3$s / %4$s', 'mirano' ), array(
			'a'=>array(
				'href'=>array(),
				'class'=>array()
			),
			'span'=>array(
				'class'=>array()
			),
			'i'=>array(
				'class'=>array()
			)
		));

		printf( $utility_text, $author, $categories_list, $write_comments, $tag_list);
	}

	static function road_entry_meta_small() {
		
		$date = sprintf( '<time class="entry-date" datetime="%3$s">%4$s</time>',
			esc_url( get_permalink() ),
			esc_attr( get_the_time() ),
			esc_attr( get_the_date( 'c' ) ),
			esc_html( get_the_date() )
		);

		$num_comments = (int)get_comments_number();
		$write_comments = '';
		if ( comments_open() ) {
			if ( $num_comments == 0 ) {
				$comments = esc_html__('0 comments', 'mirano');
			} elseif ( $num_comments > 1 ) {
				$comments = $num_comments . esc_html__(' comments', 'mirano');
			} else {
				$comments = esc_html__('1 comment', 'mirano');
			}
			$write_comments = '<a href="' . get_comments_link() .'"><i class="fa fa-comments-o"></i>'. $comments.'</a>';
		}
		
		$utility_text = wp_kses(__( '<i class="fa fa-calendar"></i>%1$s%2$s', 'mirano' ), array(
			'a'=>array(
				'href'=>array(),
				'class'=>array()
			),
			'span'=>array(
				'class'=>array()
			),
			'i'=>array(
				'class'=>array()
			)
		));
		
		printf( $utility_text, $date, $write_comments );
	}

	static function road_entry_comments() {
		
		$date = sprintf( '<time class="entry-date" datetime="%3$s">%4$s</time>',
			esc_url( get_permalink() ),
			esc_attr( get_the_time() ),
			esc_attr( get_the_date( 'c' ) ),
			esc_html( get_the_date() )
		);

		$num_comments = (int)get_comments_number();
		$write_comments = '';
		if ( comments_open() ) {
			if ( $num_comments == 0 ) {
				$comments = esc_html__('<span>0</span> comments', 'mirano');
			} elseif ( $num_comments > 1 ) {
				$comments = '<span>'.$num_comments .'</span>'. esc_html__(' comments', 'mirano');
			} else {
				$comments = esc_html__('<span>1</span> comment', 'mirano');
			}
			$write_comments = '<a href="' . get_comments_link() .'">'. $comments.'</a>';
		}
		
		$utility_text = esc_html__( '%1$s', 'mirano' );
		
		printf( $utility_text, $write_comments );
	}
	function road_register_required_plugins() {

		$plugins = array(
			array(
				'name'               => 'RoadThemes Helper',
				'slug'               => 'roadthemes-helper',
				'source'             => get_stylesheet_directory() . '/plugins/roadthemes-helper.zip',
				'required'           => true,
				'version'            => '1.0.0',
				'force_activation'   => false,
				'force_deactivation' => false,
				'external_url'       => '',
			),
			array(
				'name'               => 'Mega Main Menu',
				'slug'               => 'mega_main_menu',
				'source'             => 'http://demo.roadthemes.com/plugins/mega_main_menu.zip',
				'required'           => true,
				'external_url'       => '',
			),
			array(
				'name'               => 'Revolution Slider',
				'slug'               => 'revslider',
				'source'             => 'http://demo.roadthemes.com/plugins/revslider.zip',
				'required'           => true,
				'external_url'       => '',
			),
			array(
				'name'               => 'Visual Composer',
				'slug'               => 'js_composer',
				'source'             => 'http://demo.roadthemes.com/plugins/js_composer.zip',
				'required'           => true,
				'external_url'       => '',
			),
			
			// Plugins from the WordPress Plugin Repository.
			array(
				'name'               => 'Redux Framework',
				'slug'               => 'redux-framework',
				'required'           => true,
				'force_activation'   => false,
				'force_deactivation' => false,
			),
			array(
				'name'      => 'Contact Form 7',
				'slug'      => 'contact-form-7',
				'required'  => true,
			),
			array(
				'name'      => 'MailPoet Newsletters',
				'slug'      => 'wysija-newsletters',
				'required'  => true,
			),
			array(
				'name'      => 'Shortcodes Ultimate',
				'slug'      => 'shortcodes-ultimate',
				'required'  => true,
			),
			array(
				'name'      => 'Simple Local Avatars',
				'slug'      => 'simple-local-avatars',
				'required'  => false,
			),
			array(
				'name'      => 'TinyMCE Advanced',
				'slug'      => 'tinymce-advanced',
				'required'  => false,
			),
			array(
				'name'      => 'Widget Importer & Exporter',
				'slug'      => 'widget-importer-exporter',
				'required'  => false,
			),
			array(
				'name'      => 'WooCommerce',
				'slug'      => 'woocommerce',
				'required'  => true,
			),
			array(
				'name'      => 'YITH WooCommerce Compare',
				'slug'      => 'yith-woocommerce-compare',
				'required'  => true,
			),
			array(
				'name'      => 'YITH WooCommerce Wishlist',
				'slug'      => 'yith-woocommerce-wishlist',
				'required'  => true,
			),
			array(
				'name'      => 'YITH WooCommerce Zoom Magnifier',
				'slug'      => 'yith-woocommerce-zoom-magnifier',
				'required'  => true,
			),
		);

		/**
		 * Array of configuration settings. Amend each line as needed.
		 * If you want the default strings to be available under your own theme domain,
		 * leave the strings uncommented.
		 * Some of the strings are added into a sprintf, so see the comments at the
		 * end of each line for what each argument will be.
		 */
		$config = array(
			'default_path' => '',                      // Default absolute path to pre-packaged plugins.
			'menu'         => 'tgmpa-install-plugins', // Menu slug.
			'has_notices'  => true,                    // Show admin notices or not.
			'dismissable'  => true,                    // If false, a user cannot dismiss the nag message.
			'dismiss_msg'  => '',                      // If 'dismissable' is false, this message will be output at top of nag.
			'is_automatic' => false,                   // Automatically activate plugins after installation or not.
			'message'      => '',                      // Message to output right before the plugins table.
			'strings'      => array(
				'page_title'                      => esc_html__( 'Install Required Plugins', 'mirano' ),
				'menu_title'                      => esc_html__( 'Install Plugins', 'mirano' ),
				'installing'                      => esc_html__( 'Installing Plugin: %s', 'mirano' ), // %s = plugin name.
				'oops'                            => esc_html__( 'Something went wrong with the plugin API.', 'mirano' ),
				'notice_can_install_required'     => _n_noop( 'This theme requires the following plugin: %1$s.', 'This theme requires the following plugins: %1$s.', 'mirano' ), // %1$s = plugin name(s).
				'notice_can_install_recommended'  => _n_noop( 'This theme recommends the following plugin: %1$s.', 'This theme recommends the following plugins: %1$s.', 'mirano' ), // %1$s = plugin name(s).
				'notice_cannot_install'           => _n_noop( 'Sorry, but you do not have the correct permissions to install the %s plugin. Contact the administrator of this site for help on getting the plugin installed.', 'Sorry, but you do not have the correct permissions to install the %s plugins. Contact the administrator of this site for help on getting the plugins installed.', 'mirano' ), // %1$s = plugin name(s).
				'notice_can_activate_required'    => _n_noop( 'The following required plugin is currently inactive: %1$s.', 'The following required plugins are currently inactive: %1$s.', 'mirano' ), // %1$s = plugin name(s).
				'notice_can_activate_recommended' => _n_noop( 'The following recommended plugin is currently inactive: %1$s.', 'The following recommended plugins are currently inactive: %1$s.', 'mirano' ), // %1$s = plugin name(s).
				'notice_cannot_activate'          => _n_noop( 'Sorry, but you do not have the correct permissions to activate the %s plugin. Contact the administrator of this site for help on getting the plugin activated.', 'Sorry, but you do not have the correct permissions to activate the %s plugins. Contact the administrator of this site for help on getting the plugins activated.', 'mirano' ), // %1$s = plugin name(s).
				'notice_ask_to_update'            => _n_noop( 'The following plugin needs to be updated to its latest version to ensure maximum compatibility with this theme: %1$s.', 'The following plugins need to be updated to their latest version to ensure maximum compatibility with this theme: %1$s.', 'mirano' ), // %1$s = plugin name(s).
				'notice_cannot_update'            => _n_noop( 'Sorry, but you do not have the correct permissions to update the %s plugin. Contact the administrator of this site for help on getting the plugin updated.', 'Sorry, but you do not have the correct permissions to update the %s plugins. Contact the administrator of this site for help on getting the plugins updated.', 'mirano' ), // %1$s = plugin name(s).
				'install_link'                    => _n_noop( 'Begin installing plugin', 'Begin installing plugins', 'mirano' ),
				'activate_link'                   => _n_noop( 'Begin activating plugin', 'Begin activating plugins', 'mirano' ),
				'return'                          => esc_html__( 'Return to Required Plugins Installer', 'mirano' ),
				'plugin_activated'                => esc_html__( 'Plugin activated successfully.', 'mirano' ),
				'complete'                        => esc_html__( 'All plugins installed and activated successfully. %s', 'mirano' ), // %s = dashboard link.
				'nag_type'                        => 'updated' // Determines admin notice type - can only be 'updated', 'update-nag' or 'error'.
			)
		);
		tgmpa( $plugins, $config );
	}
}

// Instantiate theme
$miranotheme = new Mirano_Theme();

//Fix duplicate id of mega menu
function road_mega_menu_id_change($params) {
	ob_start('road_mega_menu_id_change_call_back');
}
function road_mega_menu_id_change_call_back($html){
	$html = preg_replace('/id="mega_main_menu"/', 'id="mega_main_menu_first"', $html, 1);
	$html = preg_replace('/id="mega_main_menu_ul"/', 'id="mega_main_menu_ul_first"', $html, 1);
	
	return $html;
}
add_action('wp_loaded', 'road_mega_menu_id_change');