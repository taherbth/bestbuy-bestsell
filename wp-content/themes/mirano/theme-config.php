<?php

/**
  ReduxFramework Sample Config File
  For full documentation, please visit: https://docs.reduxframework.com
 * */

if (!class_exists('Road_Theme_Config')) {

    class Road_Theme_Config {

        public $args        = array();
        public $sections    = array();
        public $theme;
        public $ReduxFramework;

        public function __construct() {

            if (!class_exists('ReduxFramework')) {
                return;
            }

            // This is needed. Bah WordPress bugs.  ;)
            if (  true == Redux_Helpers::isTheme(__FILE__) ) {
                $this->initSettings();
            } else {
                add_action('plugins_loaded', array($this, 'initSettings'), 10);
            }

        }

        public function initSettings() {

            // Just for demo purposes. Not needed per say.
            $this->theme = wp_get_theme();

            // Set the default arguments
            $this->setArguments();

            // Set a few help tabs so you can see how it's done
            $this->setHelpTabs();

            // Create the sections and fields
            $this->setSections();

            if (!isset($this->args['opt_name'])) { // No errors please
                return;
            }

            // If Redux is running as a plugin, this will remove the demo notice and links
            //add_action( 'redux/loaded', array( $this, 'remove_demo' ) );
            
            // Function to test the compiler hook and demo CSS output.
            // Above 10 is a priority, but 2 in necessary to include the dynamically generated CSS to be sent to the function.
            //add_filter('redux/options/'.$this->args['opt_name'].'/compiler', array( $this, 'compiler_action' ), 10, 3);
            
            // Change the arguments after they've been declared, but before the panel is created
            //add_filter('redux/options/'.$this->args['opt_name'].'/args', array( $this, 'change_arguments' ) );
            
            // Change the default value of a field after it's been set, but before it's been useds
            //add_filter('redux/options/'.$this->args['opt_name'].'/defaults', array( $this,'change_defaults' ) );
            
            // Dynamically add a section. Can be also used to modify sections/fields
            //add_filter('redux/options/' . $this->args['opt_name'] . '/sections', array($this, 'dynamic_section'));

            $this->ReduxFramework = new ReduxFramework($this->sections, $this->args);
        }

        /**

          This is a test function that will let you see when the compiler hook occurs.
          It only runs if a field	set with compiler=>true is changed.

         * */
        function compiler_action($options, $css, $changed_values) {
            echo '<h1>The compiler hook has run!</h1>';
            echo "<pre>";
            print_r($changed_values); // Values that have changed since the last save
            echo "</pre>";
            //print_r($options); //Option values
            //print_r($css); // Compiler selector CSS values  compiler => array( CSS SELECTORS )
        }

        /**

          Custom function for filtering the sections array. Good for child themes to override or add to the sections.
          Simply include this function in the child themes functions.php file.

          NOTE: the defined constants for URLs, and directories will NOT be available at this point in a child theme,
          so you must use get_template_directory_uri() if you want to use any of the built in icons

         * */
        function dynamic_section($sections) {
            //$sections = array();
            $sections[] = array(
                'title' => esc_html__('Section via hook', 'mirano'),
                'desc' => esc_html__('<p class="description">This is a section created by adding a filter to the sections array. Can be used by child themes to add/remove sections from the options.</p>', 'mirano'),
                'icon' => 'el-icon-paper-clip',
                // Leave this as a blank section, no options just some intro text set above.
                'fields' => array()
            );

            return $sections;
        }

        /**

          Filter hook for filtering the args. Good for child themes to override or add to the args array. Can also be used in other functions.

         * */
        function change_arguments($args) {
            //$args['dev_mode'] = true;

            return $args;
        }

        /**

          Filter hook for filtering the default value of any given field. Very useful in development mode.

         * */
        function change_defaults($defaults) {
            $defaults['str_replace'] = 'Testing filter hook!';

            return $defaults;
        }

        // Remove the demo link and the notice of integrated demo from the redux-framework plugin
        function remove_demo() {

            // Used to hide the demo mode link from the plugin page. Only used when Redux is a plugin.
            if (class_exists('ReduxFrameworkPlugin')) {
                remove_filter('plugin_row_meta', array(ReduxFrameworkPlugin::instance(), 'plugin_metalinks'), null, 2);

                // Used to hide the activation notice informing users of the demo panel. Only used when Redux is a plugin.
                remove_action('admin_notices', array(ReduxFrameworkPlugin::instance(), 'admin_notices'));
            }
        }

        public function setSections() {

            /**
              Used within different fields. Simply examples. Search for ACTUAL DECLARATION for field examples
             * */
            // Background Patterns Reader
            $sample_patterns_path   = ReduxFramework::$_dir . '../sample/patterns/';
            $sample_patterns_url    = ReduxFramework::$_url . '../sample/patterns/';
            $sample_patterns        = array();

            if (is_dir($sample_patterns_path)) :

                if ($sample_patterns_dir = opendir($sample_patterns_path)) :
                    $sample_patterns = array();

                    while (( $sample_patterns_file = readdir($sample_patterns_dir) ) !== false) {

                        if (stristr($sample_patterns_file, '.png') !== false || stristr($sample_patterns_file, '.jpg') !== false) {
                            $name = explode('.', $sample_patterns_file);
                            $name = str_replace('.' . end($name), '', $sample_patterns_file);
                            $sample_patterns[]  = array('alt' => $name, 'img' => $sample_patterns_url . $sample_patterns_file);
                        }
                    }
                endif;
            endif;

            ob_start();

            $ct             = wp_get_theme();
            $this->theme    = $ct;
            $item_name      = $this->theme->get('Name');
            $tags           = $this->theme->Tags;
            $screenshot     = $this->theme->get_screenshot();
            $class          = $screenshot ? 'has-screenshot' : '';

            $customize_title = sprintf(esc_html__('Customize &#8220;%s&#8221;', 'mirano'), $this->theme->display('Name'));
            
            ?>
            <div id="current-theme" class="<?php echo esc_attr($class); ?>">
            <?php if ($screenshot) : ?>
                <?php if (current_user_can('edit_theme_options')) : ?>
                        <a href="<?php echo wp_customize_url(); ?>" class="load-customize hide-if-no-customize" title="<?php echo esc_attr($customize_title); ?>">
                            <img src="<?php echo esc_url($screenshot); ?>" alt="<?php esc_attr__('Current theme preview', 'mirano'); ?>" />
                        </a>
                <?php endif; ?>
                    <img class="hide-if-customize" src="<?php echo esc_url($screenshot); ?>" alt="<?php esc_attr__('Current theme preview', 'mirano'); ?>" />
                <?php endif; ?>

                <h4><?php echo esc_html($this->theme->display('Name')); ?></h4>

                <div>
                    <ul class="theme-info">
                        <li><?php printf(esc_html__('By %s', 'mirano'), $this->theme->display('Author')); ?></li>
                        <li><?php printf(esc_html__('Version %s', 'mirano'), $this->theme->display('Version')); ?></li>
                        <li><?php echo '<strong>' . esc_html__('Tags', 'mirano') . ':</strong> '; ?><?php printf($this->theme->display('Tags')); ?></li>
                    </ul>
                    <p class="theme-description"><?php echo esc_html($this->theme->display('Description')); ?></p>
            <?php
            if ($this->theme->parent()) {
                printf(' <p class="howto">' . esc_html__('This <a href="%1$s">child theme</a> requires its parent theme, %2$s.', 'mirano') . '</p>', esc_html__('http://codex.wordpress.org/Child_Themes', 'mirano'), $this->theme->parent()->display('Name'));
            }
            ?>

                </div>
            </div>

            <?php
            $item_info = ob_get_contents();

            ob_end_clean();

            $sampleHTML = '';
           
            // General
            $this->sections[] = array(
                'title'     => esc_html__('General', 'mirano'),
                'desc'      => esc_html__('General theme options', 'mirano'),
                'icon'      => 'el-icon-cog',
                'fields'    => array(

                    array(
                        'id'        => 'logo_main',
                        'type'      => 'media',
                        'title'     => esc_html__('Logo', 'mirano'),
                        'compiler'  => 'true',
                        'mode'      => false,
                        'desc'      => esc_html__('Upload logo here.', 'mirano'),
                    ),
					array(
                        'id'        => 'opt-favicon',
                        'type'      => 'media',
                        'title'     => esc_html__('Favicon', 'mirano'),
                        'compiler'  => 'true',
                        'mode'      => false,
                        'desc'      => esc_html__('Upload favicon here.', 'mirano'),
                    ),
					array(
                        'id'        => 'image_error',
                        'type'      => 'media',
                        'title'     => esc_html__('Image 404', 'mirano'),
                        'compiler'  => 'true',
                        'mode'      => false,
                        'desc'      => esc_html__('Upload here.', 'mirano'),
                    ),
                ),
            );
			// Background
            $this->sections[] = array(
                'title'     => esc_html__('Background', 'mirano'),
                'desc'      => esc_html__('Use this section to upload background images, select background color', 'mirano'),
                'icon'      => 'el-icon-picture',
                'fields'    => array(
					
					array(
                        'id'        => 'background_opt',
                        'type'      => 'background',
                        'output'    => array('body'),
                        'title'     => esc_html__('Body Background', 'mirano'),
                        'subtitle'  => esc_html__('Body background with image, color. Only work with box layout', 'mirano'),
						'default'   => '#ffffff',
                    ),
                ),
            );
			// Colors
            $this->sections[] = array(
                'title'     => esc_html__('Colors', 'mirano'),
                'desc'      => esc_html__('Color options', 'mirano'),
                'icon'      => 'el-icon-tint',
                'fields'    => array(
					array(
                        'id'        => 'primary_color',
                        'type'      => 'color',
                        'title'     => esc_html__('Primary Color', 'mirano'),
                        'subtitle'  => esc_html__('Pick a color for primary color (default: #14bfcc).', 'mirano'),
						'transparent' => false,
                        'default'   => '#14bfcc',
                        'validate'  => 'color',
                    ),
					array(
                        'id'        => 'sale_color',
                        'type'      => 'color',
                        //'output'    => array(),
                        'title'     => esc_html__('Sale Label BG Color', 'mirano'),
                        'subtitle'  => esc_html__('Pick a color for bg sale label (default: #f93f25).', 'mirano'),
						'transparent' => false,
                        'default'   => '#f93f25',
                        'validate'  => 'color',
                    ),
					
					array(
                        'id'        => 'saletext_color',
                        'type'      => 'color',
                        //'output'    => array(),
                        'title'     => esc_html__('Sale Label Text Color', 'mirano'),
                        'subtitle'  => esc_html__('Pick a color for sale label text (default: #FFFFFF).', 'mirano'),
						'transparent' => false,
                        'default'   => '#ffffff',
                        'validate'  => 'color',
                    ),
					
					array(
                        'id'        => 'rate_color',
                        'type'      => 'color',
                        //'output'    => array(),
                        'title'     => esc_html__('Rating Star Color', 'mirano'),
                        'subtitle'  => esc_html__('Pick a color for star of rating (default: #f7bb39).', 'mirano'),
						'transparent' => false,
                        'default'   => '#f7bb39',
                        'validate'  => 'color',
                    ),
                ),
            );
			
			//Header
			$this->sections[] = array(
                'title'     => esc_html__('Header', 'mirano'),
                'desc'      => esc_html__('Header options', 'mirano'),
                'icon'      => 'el-icon-tasks',
                'fields'    => array(

					array(
                        'id'        => 'header_layout',
                        'type'      => 'select',
                        'title'     => esc_html__('Header Layout', 'mirano'),
                        'customizer_only'   => false,

                        //Must provide key => value pairs for select options
                        'options'   => array(
                            'default' => 'Default',
                            'second' => 'Second',
                            'third' => 'Third',
                        ),
                        'default'   => 'default'
                    ),
					array(
                        'id'        => 'mobile_menu_label',
                        'type'      => 'text',
                        'title'     => esc_html__('Mobile menu label', 'mirano'),
						'subtitle'     => esc_html__('The label for mobile menu (example: Menu, Go to...', 'mirano'),
                        'default'   => 'Menu'
                    ),
                     array(
                        'id'       => 'top_menu',
                        'type'     => 'select',
                        'data'     => 'menus',
                        'title'    => esc_html__( 'Top Menu', 'mirano' ),
                        'subtitle' => esc_html__( 'Select a menu', 'mirano' ),
                    ),
                ),
            );
			//Footer
			$this->sections[] = array(
                'title'     => __('Footer', 'redux-framework'),
                'desc'      => __('Footer options', 'redux-framework'),
                'icon'      => 'el-icon-cog',
                'fields'    => array(

                    array(
                        'id'               => 'copyright',
                        'type'             => 'editor',
                        'title'    => __('Copyright information', 'redux-framework'),
                        'subtitle'         => __('HTML tags allowed: a, br, em, strong', 'redux-framework'),
                        'default'          => 'Copyright © 2014 <a href="http://www.roadthemes.com/">Roadthemes.</a> All Rights Reserved',
                        'args'   => array(
                            'teeny'            => true,
                            'textarea_rows'    => 5,
                            'media_buttons' => false,
                        )
                    ),
                    array(
                        'id'               => 'payment_icons',
                        'type'             => 'editor',
                        'title'    => __('Payment icons', 'redux-framework'),
                        'subtitle'         => __('HTML tags allowed: a, img', 'redux-framework'),
                        'default'          => '',
                        'args'   => array(
                            'teeny'            => true,
                            'textarea_rows'    => 5,
                            'media_buttons' => true,
                        )
                    ),
                ),
            );
            $this->sections[] = array(
                'icon'       => 'el-icon-website',
                'title'      => __( 'Newsletter', 'redux-framework' ),
                'subsection' => true,
                'fields'     => array(
                    array(
                        'id'        => 'newsletter_title',
                        'type'      => 'text',
                        'title'     => __('Newsletter title', 'redux-framework'),
                        'default'   => 'Newsletter'
                    ),
                    array(
                        'id'       => 'newsletter_form',
                        'type'     => 'text',
                        'title'    => __('Newsletter form ID', 'redux-framework'),
                        'subtitle' => __('The form ID of MailPoet plugin.', 'redux-framework'),
                        'validate' => 'numeric',
                        'msg'      => 'Please enter a form ID',
                        'default'  => '1'
                    ),
                )
            );
            $this->sections[] = array(
                'icon'       => 'el-icon-website',
                'title'      => __( 'Social Icons', 'redux-framework' ),
                'subsection' => true,
                'fields'     => array(
            
                    array(
                        'id'       => 'social_icons',
                        'type'     => 'sortable',
                        'title'    => __('Social Icons', 'redux-framework'),
                        'subtitle' => __('Enter social links', 'redux-framework'),
                        'desc'     => __('Drag/drop to re-arrange', 'redux-framework'),
                        'mode'     => 'text',
                        'options'  => array(
                            'facebook'     => '',
                            'twitter'     => '',
                            'tumblr'     => '',
                            'pinterest'     => '',
                            'google-plus'     => '',
                            'linkedin'     => '',
                            'behance'     => '',
                            'dribbble'     => '',
                            'youtube'     => '',
                            'vimeo'     => '',
                            'rss'     => '',
                        ),
                        'default' => array(
                            'facebook'     => 'https://www.facebook.com/',
                            'twitter'     => 'https://twitter.com/',
                            'tumblr'     => 'https://www.tumblr.com/',
                            'pinterest'     => '',
                            'google-plus'     => 'https://plus.google.com/',
                            'linkedin'     => '',
                            'behance'     => '',
                            'dribbble'     => 'https://dribbble.com/',
                            'youtube'     => '',
                            'vimeo'     => '',
                            'rss'     => '',
                        ),
                    ),
                )
            );
            $this->sections[] = array(
                'icon'       => 'el-icon-website',
                'title'      => __( 'Contact Us', 'redux-framework' ),
                'subsection' => true,
                'fields'     => array(
                    array(
                        'id'        => 'contact_title',
                        'type'      => 'text',
                        'title'     => __('Contact Us title', 'redux-framework'),
                        'default'   => 'Contact Us'
                    ),
                    array(
                        'id'=>'contact_us',
                        'type' => 'textarea',
                        'title' => __('Contact Us', 'redux-framework'), 
                        'subtitle'         => __('HTML tags allowed: a, img, br, em, strong, p, ul, li', 'redux-framework'),
                        'default' => '',
                    ),
                )
            );
            $this->sections[] = array(
                'icon'       => 'el-icon-website',
                'title'      => __( 'Menus', 'redux-framework' ),
                'subsection' => true,
                'fields'     => array(
                    array(
                        'id'       => 'footer_menu1',
                        'type'     => 'select',
                        'data'     => 'menus',
                        'title'    => __( 'Menu #1', 'redux-framework' ),
                        'subtitle' => __( 'Select a menu', 'redux-framework' ),
                    ),
                    array(
                        'id'       => 'footer_menu2',
                        'type'     => 'select',
                        'data'     => 'menus',
                        'title'    => __( 'Menu #2', 'redux-framework' ),
                        'subtitle' => __( 'Select a menu', 'redux-framework' ),
                    ),
                    array(
                        'id'       => 'footer_menu3',
                        'type'     => 'select',
                        'data'     => 'menus',
                        'title'    => __( 'Menu #3', 'redux-framework' ),
                        'subtitle' => __( 'Select a menu', 'redux-framework' ),
                    ),
                    array(
                        'id'       => 'footer_menu_bottom',
                        'type'     => 'select',
                        'data'     => 'menus',
                        'title'    => __( 'Menu Bottom', 'redux-framework' ),
                        'subtitle' => __( 'Select a menu', 'redux-framework' ),
                    ),
                )
            );
			
			//Fonts
			$this->sections[] = array(
                'title'     => esc_html__('Fonts', 'mirano'),
                'desc'      => esc_html__('Fonts options', 'mirano'),
                'icon'      => 'el-icon-font',
                'fields'    => array(

                    array(
                        'id'            => 'bodyfont',
                        'type'          => 'typography',
                        'title'         => esc_html__('Body font', 'mirano'),
                        //'compiler'      => true,  // Use if you want to hook in your own CSS compiler
                        'google'        => true,    // Disable google fonts. Won't work if you haven't defined your google api key
                        'font-backup'   => true,    // Select a backup non-google font in addition to a google font
                        //'font-style'    => false, // Includes font-style and weight. Can use font-style or font-weight to declare
                        'subsets'       => false, // Only appears if google is true and subsets not set to false
						'text-align'   => false,
                        //'font-size'     => false,
                        //'line-height'   => false,
                        //'word-spacing'  => true,  // Defaults to false
                        //'letter-spacing'=> true,  // Defaults to false
                        //'color'         => false,
                        //'preview'       => false, // Disable the previewer
                        'all_styles'    => true,    // Enable all Google Font style/weight variations to be added to the page
                        'output'        => array('body'), // An array of CSS selectors to apply this font style to dynamically
                        //'compiler'      => array('h2.site-description-compiler'), // An array of CSS selectors to apply this font style to dynamically
                        'units'         => 'px', // Defaults to px
                        'subtitle'      => esc_html__('Main body font.', 'mirano'),
                        'default'       => array(
                            'color'         => '#444',
                            'font-weight'    => '400',
                            'font-family'   => 'Arial',
                            'google'        => true,
                            'font-size'     => '13px',
                            'line-height'   => '20px'),
                    ),
					array(
                        'id'            => 'headingfont',
                        'type'          => 'typography',
                        'title'         => esc_html__('Heading font', 'mirano'),
                        //'compiler'      => true,  // Use if you want to hook in your own CSS compiler
                        'google'        => true,    // Disable google fonts. Won't work if you haven't defined your google api key
                        'font-backup'   => false,    // Select a backup non-google font in addition to a google font
                        //'font-style'    => false, // Includes font-style and weight. Can use font-style or font-weight to declare
                        'subsets'       => false, // Only appears if google is true and subsets not set to false
                        'font-size'     => false,
                        'line-height'   => false,
						'text-align'   => false,
                        //'word-spacing'  => true,  // Defaults to false
                        //'letter-spacing'=> true,  // Defaults to false
                        //'color'         => false,
                        //'preview'       => false, // Disable the previewer
                        'all_styles'    => true,    // Enable all Google Font style/weight variations to be added to the page
                        //'output'        => array('h1, h2, h3, h4, h5, h6'), // An array of CSS selectors to apply this font style to dynamically
                        //'compiler'      => array('h2.site-description-compiler'), // An array of CSS selectors to apply this font style to dynamically
                        'units'         => 'px', // Defaults to px
                        'subtitle'      => esc_html__('Heading font.', 'mirano'),
                        'default'       => array(
                            'color'         => '#636363',
                            'font-weight'    => '700',
                            'font-family'   => 'Arial',
                            'google'        => true,
						),
                    ),
					array(
                        'id'            => 'menufont',
                        'type'          => 'typography',
                        'title'         => esc_html__('Menu font', 'mirano'),
                        //'compiler'      => true,  // Use if you want to hook in your own CSS compiler
                        'google'        => true,    // Disable google fonts. Won't work if you haven't defined your google api key
                        'font-backup'   => false,    // Select a backup non-google font in addition to a google font
                        //'font-style'    => false, // Includes font-style and weight. Can use font-style or font-weight to declare
                        'subsets'       => false, // Only appears if google is true and subsets not set to false
                        'font-size'     => false,
                        'line-height'   => false,
						'text-align'   => false,
                        //'word-spacing'  => true,  // Defaults to false
                        //'letter-spacing'=> true,  // Defaults to false
                        //'color'         => false,
                        //'preview'       => false, // Disable the previewer
                        'all_styles'    => true,    // Enable all Google Font style/weight variations to be added to the page
                        //'output'        => array('h1, h2, h3, h4, h5, h6'), // An array of CSS selectors to apply this font style to dynamically
                        //'compiler'      => array('h2.site-description-compiler'), // An array of CSS selectors to apply this font style to dynamically
                        'units'         => 'px', // Defaults to px
                        'subtitle'      => esc_html__('Menu font.', 'mirano'),
                        'default'       => array(
                            'color'         => '#3e3e3e',
                            'font-style'    => '700',
                            'font-family'   => 'Montserrat',
                            'google'        => true,
						),
                    ),
                    array(
                        'id'            => 'pricefont',
                        'type'          => 'typography',
                        'title'         => esc_html__('Price font', 'mirano'),
                        //'compiler'      => true,  // Use if you want to hook in your own CSS compiler
                        'google'        => true,    // Disable google fonts. Won't work if you haven't defined your google api key
                        'font-backup'   => false,    // Select a backup non-google font in addition to a google font
                        //'font-style'    => false, // Includes font-style and weight. Can use font-style or font-weight to declare
                        'subsets'       => false, // Only appears if google is true and subsets not set to false
                        'font-size'     => false,
                        'line-height'   => false,
                        'text-align'   => false,
                        //'word-spacing'  => true,  // Defaults to false
                        //'letter-spacing'=> true,  // Defaults to false
                        //'color'         => false,
                        //'preview'       => false, // Disable the previewer
                        'all_styles'    => true,    // Enable all Google Font style/weight variations to be added to the page
                        //'output'        => array('h1, h2, h3, h4, h5, h6'), // An array of CSS selectors to apply this font style to dynamically
                        //'compiler'      => array('h2.site-description-compiler'), // An array of CSS selectors to apply this font style to dynamically
                        'units'         => 'px', // Defaults to px
                        'subtitle'      => esc_html__('Price font.', 'mirano'),
                        'default'       => array(
                            'color'         => '#9f9f9f',
                            'font-style'    => '500',
                            'font-family'   => 'Montserrat',
                            'google'        => true,
                        ),
                    ),
                ),
            );
			
			// Layout
            $this->sections[] = array(
                'title'     => esc_html__('Layout', 'mirano'),
                'desc'      => esc_html__('Select page layout: Box or Full Width', 'mirano'),
                'icon'      => 'el-icon-align-justify',
                'fields'    => array(
					array(
						'id'       => 'page_layout',
						'type'     => 'select',
						'multi'    => false,
						'title'    => esc_html__('Page Layout', 'mirano'),
						'options'  => array(
							'full' => 'Full Width',
							'box' => 'Box'
						),
						'default'  => 'full'
					),
                    array(
                        'id'        => 'preset_option',
                        'type'      => 'select',
                        'title'     => esc_html__('Preset', 'mirano'),
                        'subtitle'      => esc_html__('Select a preset to quickly apply pre-defined colors and fonts', 'mirano'),
                        'customizer_only'   => false,
                        'options'   => array(
                            '1' => 'Use options',
                            '2' => 'Preset 2',
                            '3' => 'Preset 3',
                            '4' => 'Preset 4',
                            '5' => 'Preset 5',
                            '6' => 'Preset 6',
                        ),
                        'default'   => '1'
                    ),
					array(
                        'id'        => 'enable_sswitcher',
                        'type'      => 'switch',
                        'title'     => esc_html__('Show Style Switcher', 'mirano'),
						'subtitle'     => esc_html__('The style switcher is only for preview on front-end', 'mirano'),
						'default'   => false,
                    ),
                ),
            );
			
			//Brand logos
			$this->sections[] = array(
                'title'     => esc_html__('Brand Logos', 'mirano'),
                'desc'      => esc_html__('Upload brand logos and links', 'mirano'),
                'icon'      => 'el-icon-cog',
                'fields'    => array(
					array(
                        'id'        => 'brand_title',
                        'type'      => 'text',
                        'title'     => esc_html__('Brand title', 'mirano'),
                        'default'   => 'Our Brands'
                    ),
					array(
						'id'        => 'brandnumber',
						'type'      => 'slider',
						'title'     => esc_html__('Number of logos per page', 'mirano'),
						'desc'      => esc_html__('Number of logos per page, default value: 6', 'mirano'),
						"default"   => 6,
						"min"       => 1,
						"step"      => 1,
						"max"       => 12,
						'display_value' => 'text'
					),
					array(
						'id'       => 'brandscroll',
						'type'     => 'switch',
						'title'    => esc_html__('Auto scroll', 'mirano'),
						'default'  => true,
					),
					array(
						'id'        => 'brandscrollnumber',
						'type'      => 'slider',
						'title'     => esc_html__('Scroll amount', 'mirano'),
						'desc'      => esc_html__('Number of logos to scroll one time, default value: 2', 'mirano'),
						"default"   => 2,
						"min"       => 1,
						"step"      => 1,
						"max"       => 12,
						'display_value' => 'text'
					),
					array(
						'id'        => 'brandpause',
						'type'      => 'slider',
						'title'     => esc_html__('Pause in (seconds)', 'mirano'),
						'desc'      => esc_html__('Pause time, default value: 3000', 'mirano'),
						"default"   => 3000,
						"min"       => 1000,
						"step"      => 500,
						"max"       => 10000,
						'display_value' => 'text'
					),
					array(
						'id'        => 'brandanimate',
						'type'      => 'slider',
						'title'     => esc_html__('Animate in (seconds)', 'mirano'),
						'desc'      => esc_html__('Animate time, default value: 2000', 'mirano'),
						"default"   => 2000,
						"min"       => 300,
						"step"      => 100,
						"max"       => 5000,
						'display_value' => 'text'
					),
					array(
						'id'          => 'brand_logos',
						'type'        => 'slides',
						'title'       => esc_html__('Logos', 'mirano'),
						'desc'        => esc_html__('Upload logo image and enter logo link.', 'mirano'),
						'placeholder' => array(
							'title'           => esc_html__('Title', 'mirano'),
							'description'     => esc_html__('Description', 'mirano'),
							'url'             => esc_html__('Link', 'mirano'),
						),
					),
                ),
            );
			
			// Sidebar
			$this->sections[] = array(
                'title'     => esc_html__('Sidebar', 'mirano'),
                'desc'      => esc_html__('Sidebar options', 'mirano'),
                'icon'      => 'el-icon-cog',
                'fields'    => array(
					array(
						'id'       => 'sidebar_pos',
						'type'     => 'radio',
						'title'    => esc_html__('Main Sidebar Position', 'mirano'),
						'subtitle'      => esc_html__('Sidebar on category page', 'mirano'),
						'options'  => array(
							'left' => 'Left',
							'right' => 'Right'),
						'default'  => 'left'
					),
					array(
						'id'       => 'sidebarse_pos',
						'type'     => 'radio',
						'title'    => esc_html__('Secondary Sidebar Position', 'mirano'),
						'subtitle'      => esc_html__('Sidebar on pages', 'mirano'),
						'options'  => array(
							'left' => 'Left',
							'right' => 'Right'),
						'default'  => 'left'
					),
					array(
						'id'       => 'sidebarblog_pos',
						'type'     => 'radio',
						'title'    => esc_html__('Blog Sidebar Position', 'mirano'),
						'subtitle'      => esc_html__('Sidebar on Blog pages', 'mirano'),
						'options'  => array(
							'left' => 'Left',
							'right' => 'Right'),
						'default'  => 'right'
					),
                ),
            );
			
			// Product
            $this->sections[] = array(
                'title'     => esc_html__('Product', 'mirano'),
                'desc'      => esc_html__('Use this section to select options for product', 'mirano'),
                'icon'      => 'el-icon-tags',
                'fields'    => array(
					array(
                        'id'        => 'shop_layout',
                        'type'      => 'select',
                        'title'     => esc_html__('Shop Layout', 'mirano'),
                        'customizer_only'   => false,
                        'options'   => array(
							'sidebar' => 'Sidebar',
                            'fullwidth' => 'Full Width',
                        ),
                        'default'   => 'sidebar'
                    ),
					array(
                        'id'        => 'default_view',
                        'type'      => 'select',
                        'title'     => esc_html__('Shop default view', 'mirano'),
                        'customizer_only'   => false,
                        'options'   => array(
							'grid-view' => 'Grid View',
                            'list-view' => 'List View',
                        ),
                        'default'   => 'grid-view'
                    ),
					array(
						'id'        => 'product_per_page',
						'type'      => 'slider',
						'title'     => esc_html__('Products per page', 'mirano'),
						'subtitle'      => esc_html__('Amount of products per page on category page', 'mirano'),
						"default"   => 9,
						"min"       => 3,
						"step"      => 1,
						"max"       => 48,
						'display_value' => 'text'
					),
                    array(
                        'id'        => 'product_per_row',
                        'type'      => 'slider',
                        'title'     => esc_html__('Product columns', 'mirano'),
                        'subtitle'      => esc_html__('Amount of product columns on category page', 'mirano'),
                        'desc'      => esc_html__('Only works with: 1, 2, 3, 4, 6', 'mirano'),
                        "default"   => 3,
                        "min"       => 1,
                        "step"      => 1,
                        "max"       => 4,
                        'display_value' => 'text'
                    ),
                    array(
                        'id'        => 'product_per_row_fw',
                        'type'      => 'slider',
                        'title'     => esc_html__('Product columns on full width shop', 'mirano'),
                        'subtitle'      => esc_html__('Amount of product columns on full width category page', 'mirano'),
                        'desc'      => esc_html__('Only works with: 1, 2, 3, 4, 6', 'mirano'),
                        "default"   => 4,
                        "min"       => 1,
                        "step"      => 1,
                        "max"       => 6,
                        'display_value' => 'text'
                    ),
					array(
						'id'       => 'second_image',
						'type'     => 'switch',
						'title'    => esc_html__('Use secondary product image', 'mirano'),
						'default'  => false,
					),
					array(
                        'id'        => 'related_title',
                        'type'      => 'text',
                        'title'     => esc_html__('Related products title', 'mirano'),
                        'default'   => 'Related Products'
                    ),
					array(
						'id'        => 'related_amount',
						'type'      => 'slider',
						'title'     => esc_html__('Number of related products', 'mirano'),
						"default"   => 6,
						"min"       => 4,
						"step"      => 1,
						"max"       => 16,
						'display_value' => 'text'
					),

                    array(
                        'id'        => 'upsells_title',
                        'type'      => 'text',
                        'title'     => esc_html__('Up-Sells title', 'mirano'),
                        'default'   => 'Up-Sells'
                    ),
                     array(
                      'id'        => 'upsells_amount',
                      'type'      => 'slider',
                      'title'     => esc_html__('Number of upsells products', 'mirano'),
                      "default"   => 8,
                      "min"       => 3,
                      "step"      => 1,
                      "max"       => 16,
                      'display_value' => 'text'
                     ),

					array(
                        'id'        => 'crosssells_title',
                        'type'      => 'text',
                        'title'     => esc_html__('Cross-Sells title', 'mirano'),
                        'default'   => 'Cross-Sells'
                    ),
					array(
                        'id'        => 'detail_link_text',
                        'type'      => 'text',
                        'title'     => esc_html__('View details text', 'mirano'),
                        'default'   => 'View details'
                    ),
					array(
                        'id'        => 'quickview_link_text',
                        'type'      => 'text',
                        'title'     => esc_html__('View all features text', 'mirano'),
						'desc'      => esc_html__('This is the text on quick view box', 'mirano'),
                        'default'   => 'See all features'
                    ),
					array(
						'id'=>'share_head_code',
						'type' => 'textarea',
						'title' => esc_html__('ShareThis/AddThis head tag', 'mirano'), 
						'desc' => esc_html__('Paste your ShareThis or AddThis head tag here', 'redux-framework-demo'),
						'default' => '',
					),
					array(
						'id'=>'share_code',
						'type' => 'textarea',
						'title' => esc_html__('ShareThis/AddThis code', 'mirano'), 
						'desc' => esc_html__('Paste your ShareThis or AddThis code here', 'redux-framework-demo'),
						'default' => ''
					),
                ),
            );
			
            // Newsletter
            $this->sections[] = array(
                'icon'       => 'el-icon-website',
                'title'      => esc_html__( 'Newsletter', 'mirano' ),
                'subsection' => true,
                'fields'     => array(
                     array(
                        'id'               => 'social_newsletter_title',
                        'type'             => 'editor',
                        'title'    => esc_html__('Social Newsletter Title', 'mirano'),
                        'subtitle'         => esc_html__('HTML tags allowed: a, img', 'mirano'),
                        'default'          => '<h3>Stay up to date</h3>
                                                <h1>sign up for newsletter</h1>',
                        'args'   => array(
                                'teeny'            => true,
                                'textarea_rows'    => 5,
                            )
                        ),
                    array(
                        'id'       => 'newsletter_form',
                        'type'     => 'text',
                        'title'    => esc_html__('Newsletter form ID', 'mirano'),
                        'subtitle' => esc_html__('The form ID of MailPoet plugin.', 'mirano'),
                        'validate' => 'numeric',
                        'msg'      => 'Please enter a form ID',
                        'default'  => '1'
                    ),
                )
            );

			// Blog options
            $this->sections[] = array(
                'title'     => esc_html__('Blog', 'mirano'),
                'desc'      => esc_html__('Use this section to select options for blog', 'mirano'),
                'icon'      => 'el-icon-file',
                'fields'    => array(
					
					array(
                        'id'        => 'blog_layout',
                        'type'      => 'select',
                        'title'     => esc_html__('Blog Layout', 'mirano'),
                        'customizer_only'   => false,
                        'options'   => array(
							'sidebar' => 'Sidebar',
                            'nosidebar' => 'None Sidebar',
                        ),
                        'default'   => 'sidebar'
                    ),
					array(
                        'id'        => 'readmore_text',
                        'type'      => 'text',
                        'title'     => esc_html__('Read more text', 'mirano'),
                        'default'   => 'read more'
                    ),
					array(
						'id'        => 'excerpt_length',
						'type'      => 'slider',
						'title'     => esc_html__('Excerpt length on blog page', 'mirano'),
						"default"   => 22,
						"min"       => 10,
						"step"      => 2,
						"max"       => 120,
						'display_value' => 'text'
					),
					array(
						'id'       => 'blogscroll',
						'type'     => 'switch',
						'title'    => esc_html__('Latest posts auto scroll', 'mirano'),
						'default'  => false,
					),
					array(
						'id'        => 'blogpause',
						'type'      => 'slider',
						'title'     => esc_html__('Pause in (seconds)', 'mirano'),
						'desc'      => esc_html__('Pause time, default value: 3000', 'mirano'),
						"default"   => 3000,
						"min"       => 1000,
						"step"      => 500,
						"max"       => 10000,
						'display_value' => 'text'
					),
					array(
						'id'        => 'bloganimate',
						'type'      => 'slider',
						'title'     => esc_html__('Animate in (seconds)', 'mirano'),
						'desc'      => esc_html__('Animate time, default value: 2000', 'mirano'),
						"default"   => 2000,
						"min"       => 300,
						"step"      => 100,
						"max"       => 5000,
						'display_value' => 'text'
					),
                ),
            );
			
			// Contact Map
            $this->sections[] = array(
                'title'     => esc_html__('Contact Map', 'mirano'),
                'desc'      => esc_html__('Use this section to select options for Google Map on contact page', 'mirano'),
                'icon'      => 'el-icon-flag',
                'fields'    => array(
					array(
                        'id'        => 'enable_map',
                        'type'      => 'switch',
                        'title'     => esc_html__('Show map', 'mirano'),
						'subtitle'     => esc_html__('Show map on contact page', 'mirano'),
						'default'   => true,
                    ),
					array(
						'id'       => 'address_by',
						'type'     => 'radio',
						'title'    => esc_html__('Locate by', 'mirano'),
						'subtitle'      => esc_html__('Locate marker by address or coordinate', 'mirano'),
						'options'  => array(
							'address' => 'Address',
							'coordinate' => 'Coordinate'
						),
						'default'  => 'address'
					),
					array(
						'id'               => 'map_desc',
						'type'             => 'editor',
						'title'    => esc_html__('Map description', 'mirano'),
						'subtitle' => esc_html__('The text on map popup', 'mirano'),
						'default'          => '',
						'args'   => array(
							'teeny'            => true,
							'textarea_rows'    => 5,
							'media_buttons'	=> false,
						)
					),
					array(
                        'id'        => 'map_lat',
                        'type'      => 'text',
                        'title'     => esc_html__('Latitude', 'mirano'),
                        'default'   => '51.50657'
                    ),
					array(
                        'id'        => 'map_long',
                        'type'      => 'text',
                        'title'     => esc_html__('Longtitude', 'mirano'),
                        'default'   => '-0.13408'
                    ),
					array(
                        'id'        => 'map_address',
                        'type'      => 'text',
                        'title'     => esc_html__('Address', 'mirano'),
                        'default'   => 'Pall Mall, London England'
                    ),
					array(
						'id'        => 'map_zoom',
						'type'      => 'slider',
						'title'     => esc_html__('Zoom level', 'mirano'),
						"default"   => 17,
						"min"       => 0,
						"step"      => 1,
						"max"       => 21,
						'display_value' => 'text'
					),
					array(
                        'id'        => 'map_marker',
                        'type'      => 'media',
                        'title'     => esc_html__('Marker', 'mirano'),
                        'compiler'  => 'true',
                        'mode'      => false, // Can be set to false to allow any media type, or can also be set to any mime type.
                        'desc'      => esc_html__('Upload marker image here, the image size is 32x47 pixels.', 'mirano'),
                    ),
                ),
            );
			
			// Custom CSS
            $this->sections[] = array(
                'title'     => esc_html__('Custom CSS', 'mirano'),
                'desc'      => esc_html__('Add your Custom CSS code', 'mirano'),
                'icon'      => 'el-icon-pencil',
                'fields'    => array(
					array(
						'id'       => 'custom_css',
						'type'     => 'ace_editor',
						'title'    => esc_html__('CSS Code', 'mirano'),
						'subtitle' => esc_html__('Paste your CSS code here.', 'mirano'),
						'mode'     => 'css',
						'theme'    => 'monokai', //chrome
						'default'  => ""
					),
                ),
            );
			
			// Less Compiler
            $this->sections[] = array(
                'title'     => esc_html__('Less Compiler', 'mirano'),
                'desc'      => esc_html__('Turn on this option to apply all theme options. Turn of when you have finished changing theme options and your site is ready.', 'mirano'),
                'icon'      => 'el-icon-wrench',
                'fields'    => array(
					array(
                        'id'        => 'enable_less',
                        'type'      => 'switch',
                        'title'     => esc_html__('Enable Less Compiler', 'mirano'),
						'default'   => true,
                    ),
                ),
            );
			
            $theme_info  = '<div class="redux-framework-section-desc">';
            $theme_info .= '<p class="redux-framework-theme-data description theme-uri">' . esc_html__('<strong>Theme URL:</strong> ', 'mirano') . '<a href="' . $this->theme->get('ThemeURI') . '" target="_blank">' . $this->theme->get('ThemeURI') . '</a></p>';
            $theme_info .= '<p class="redux-framework-theme-data description theme-author">' . esc_html__('<strong>Author:</strong> ', 'mirano') . $this->theme->get('Author') . '</p>';
            $theme_info .= '<p class="redux-framework-theme-data description theme-version">' . esc_html__('<strong>Version:</strong> ', 'mirano') . $this->theme->get('Version') . '</p>';
            $theme_info .= '<p class="redux-framework-theme-data description theme-description">' . $this->theme->get('Description') . '</p>';
            $tabs = $this->theme->get('Tags');
            if (!empty($tabs)) {
                $theme_info .= '<p class="redux-framework-theme-data description theme-tags">' . esc_html__('<strong>Tags:</strong> ', 'mirano') . implode(', ', $tabs) . '</p>';
            }
            $theme_info .= '</div>';
            
            $this->sections[] = array(
                'title'     => esc_html__('Import / Export', 'mirano'),
                'desc'      => esc_html__('Import and Export your Redux Framework settings from file, text or URL.', 'mirano'),
                'icon'      => 'el-icon-refresh',
                'fields'    => array(
                    array(
                        'id'            => 'opt-import-export',
                        'type'          => 'import_export',
                        'title'         => 'Import Export',
                        'subtitle'      => 'Save and restore your Redux options',
                        'full_width'    => false,
                    ),
                ),
            );

            $this->sections[] = array(
                'icon'      => 'el-icon-info-sign',
                'title'     => esc_html__('Theme Information', 'mirano'),
                //'desc'      => esc_html__('<p class="description">This is the Description. Again HTML is allowed</p>', 'mirano'),
                'fields'    => array(
                    array(
                        'id'        => 'opt-raw-info',
                        'type'      => 'raw',
                        'content'   => $item_info,
                    )
                ),
            );
        }

        public function setHelpTabs() {

            // Custom page help tabs, displayed using the help API. Tabs are shown in order of definition.
            $this->args['help_tabs'][] = array(
                'id'        => 'redux-help-tab-1',
                'title'     => esc_html__('Theme Information 1', 'mirano'),
                'content'   => esc_html__('<p>This is the tab content, HTML is allowed.</p>', 'mirano')
            );

            $this->args['help_tabs'][] = array(
                'id'        => 'redux-help-tab-2',
                'title'     => esc_html__('Theme Information 2', 'mirano'),
                'content'   => esc_html__('<p>This is the tab content, HTML is allowed.</p>', 'mirano')
            );

            // Set the help sidebar
            $this->args['help_sidebar'] = esc_html__('<p>This is the sidebar content, HTML is allowed.</p>', 'mirano');
        }

        /**

          All the possible arguments for Redux.
          For full documentation on arguments, please refer to: https://github.com/ReduxFramework/ReduxFramework/wiki/Arguments

         * */
        public function setArguments() {

            $theme = wp_get_theme(); // For use with some settings. Not necessary.

            $this->args = array(
                // TYPICAL -> Change these values as you need/desire
                'opt_name'          => 'road_opt',            // This is where your data is stored in the database and also becomes your global variable name.
                'display_name'      => $theme->get('Name'),     // Name that appears at the top of your panel
                'display_version'   => $theme->get('Version'),  // Version that appears at the top of your panel
                'menu_type'         => 'menu',                  //Specify if the admin menu should appear or not. Options: menu or submenu (Under appearance only)
                'allow_sub_menu'    => true,                    // Show the sections below the admin menu item or not
                'menu_title'        => esc_html__('Theme Options', 'mirano'),
                'page_title'        => esc_html__('Theme Options', 'mirano'),
                
                // You will need to generate a Google API key to use this feature.
                // Please visit: https://developers.google.com/fonts/docs/developer_api#Auth
                'google_api_key' => '', // Must be defined to add google fonts to the typography module
                
                'async_typography'  => true,                    // Use a asynchronous font on the front end or font string
                //'disable_google_fonts_link' => true,                    // Disable this in case you want to create your own google fonts loader
                'admin_bar'         => true,                    // Show the panel pages on the admin bar
                'global_variable'   => '',                      // Set a different name for your global variable other than the opt_name
                'dev_mode'          => false,                    // Show the time the page took to load, etc
                'customizer'        => true,                    // Enable basic customizer support
                //'open_expanded'     => true,                    // Allow you to start the panel in an expanded way initially.
                //'disable_save_warn' => true,                    // Disable the save warning when a user changes a field

                // OPTIONAL -> Give you extra features
                'page_priority'     => null,                    // Order where the menu appears in the admin area. If there is any conflict, something will not show. Warning.
                'page_parent'       => 'themes.php',            // For a full list of options, visit: http://codex.wordpress.org/Function_Reference/add_submenu_page#Parameters
                'page_permissions'  => 'manage_options',        // Permissions needed to access the options panel.
                'menu_icon'         => '',                      // Specify a custom URL to an icon
                'last_tab'          => '',                      // Force your panel to always open to a specific tab (by id)
                'page_icon'         => 'icon-themes',           // Icon displayed in the admin panel next to your menu_title
                'page_slug'         => '_options',              // Page slug used to denote the panel
                'save_defaults'     => true,                    // On load save the defaults to DB before user clicks save or not
                'default_show'      => false,                   // If true, shows the default value next to each field that is not the default value.
                'default_mark'      => '',                      // What to print by the field's title if the value shown is default. Suggested: *
                'show_import_export' => true,                   // Shows the Import/Export panel when not used as a field.
                
                // CAREFUL -> These options are for advanced use only
                'transient_time'    => 60 * MINUTE_IN_SECONDS,
                'output'            => true,                    // Global shut-off for dynamic CSS output by the framework. Will also disable google fonts output
                'output_tag'        => true,                    // Allows dynamic CSS to be generated for customizer and google fonts, but stops the dynamic CSS from going to the head
                // 'footer_credit'     => '',                   // Disable the footer credit of Redux. Please leave if you can help it.
                
                // FUTURE -> Not in use yet, but reserved or partially implemented. Use at your own risk.
                'database'              => '', // possible: options, theme_mods, theme_mods_expanded, transient. Not fully functional, warning!
                'system_info'           => false, // REMOVE

                // HINTS
                'hints' => array(
                    'icon'          => 'icon-question-sign',
                    'icon_position' => 'right',
                    'icon_color'    => 'lightgray',
                    'icon_size'     => 'normal',
                    'tip_style'     => array(
                        'color'         => 'light',
                        'shadow'        => true,
                        'rounded'       => false,
                        'style'         => '',
                    ),
                    'tip_position'  => array(
                        'my' => 'top left',
                        'at' => 'bottom right',
                    ),
                    'tip_effect'    => array(
                        'show'          => array(
                            'effect'        => 'slide',
                            'duration'      => '500',
                            'event'         => 'mouseover',
                        ),
                        'hide'      => array(
                            'effect'    => 'slide',
                            'duration'  => '500',
                            'event'     => 'click mouseleave',
                        ),
                    ),
                )
            );


            // SOCIAL ICONS -> Setup custom links in the footer for quick links in your panel footer icons.
            $this->args['share_icons'][] = array(
                'url'   => 'https://github.com/ReduxFramework/ReduxFramework',
                'title' => 'Visit us on GitHub',
                'icon'  => 'el-icon-github'
                //'img'   => '', // You can use icon OR img. IMG needs to be a full URL.
            );
            $this->args['share_icons'][] = array(
                'url'   => 'https://www.facebook.com/pages/Redux-Framework/243141545850368',
                'title' => 'Like us on Facebook',
                'icon'  => 'el-icon-facebook'
            );
            $this->args['share_icons'][] = array(
                'url'   => 'http://twitter.com/reduxframework',
                'title' => 'Follow us on Twitter',
                'icon'  => 'el-icon-twitter'
            );
            $this->args['share_icons'][] = array(
                'url'   => 'http://www.linkedin.com/company/redux-framework',
                'title' => 'Find us on LinkedIn',
                'icon'  => 'el-icon-linkedin'
            );

            // Panel Intro text -> before the form
            if (!isset($this->args['global_variable']) || $this->args['global_variable'] !== false) {
                if (!empty($this->args['global_variable'])) {
                    $v = $this->args['global_variable'];
                } else {
                    $v = str_replace('-', '_', $this->args['opt_name']);
                }
                //$this->args['intro_text'] = sprintf(esc_html__('<p>Did you know that Redux sets a global variable for you? To access any of your saved options from within your code you can use your global variable: <strong>$%1$s</strong></p>', 'mirano'), $v);
            } else {
                //$this->args['intro_text'] = esc_html__('<p>This text is displayed above the options panel. It isn\'t required, but more info is always better! The intro_text field accepts all HTML.</p>', 'mirano');
            }

            // Add content after the form.
            //$this->args['footer_text'] = esc_html__('<p>This text is displayed below the options panel. It isn\'t required, but more info is always better! The footer_text field accepts all HTML.</p>', 'mirano');
        }

    }
    
    global $reduxConfig;
    $reduxConfig = new Road_Theme_Config();
}

/**
  Custom function for the callback referenced above
 */
if (!function_exists('redux_my_custom_field')):
    function redux_my_custom_field($field, $value) {
        print_r($field);
        echo '<br/>';
        print_r($value);
    }
endif;

/**
  Custom function for the callback validation referenced above
 * */
if (!function_exists('redux_validate_callback_function')):
    function redux_validate_callback_function($field, $value, $existing_value) {
        $error = false;
        $value = 'just testing';

        /*
          do your validation

          if(something) {
            $value = $value;
          } elseif(something else) {
            $error = true;
            $value = $existing_value;
            $field['msg'] = 'your custom error message';
          }
         */

        $return['value'] = $value;
        if ($error == true) {
            $return['error'] = $field;
        }
        return $return;
    }
endif;
