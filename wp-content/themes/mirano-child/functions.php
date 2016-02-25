<?php
/**
 * Mirano Functions Library
 * Here we will define all functions that will be used on Mirano Child Theme.
 */

/******************************************/
/* Add your functions after this */
/******************************************/ 

show_admin_bar( false );

add_action('after_setup_theme', 'mirano_child_setup' , 11);

function mirano_child_setup(){
    /*Add your Hooks , Filters and Theme Support after This*/
    define('BESTBUY_BESTSELL_DIR', get_stylesheet_directory_uri());
    /*
    * Include WP_List_Table class from admin, Need it to display wordpress standard
    * Custom Table Display
    */
    if ( ! class_exists( 'WP_List_Table' ) ) {
        require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
    }
    if ( ! class_exists( 'Bestbuybestsell_Interest' ) ){
        require_once( 'admin/classess/class-bestbuybestsell-interest.php' );
    }
    /*
    * Bestbuy-bestsell Business Menu For Admin Panel
    * Display Admin Menu
    */
    add_action( 'admin_menu', 'bestbuy_bestsell_business_menu' );
    function bestbuy_bestsell_business_menu() {
        add_menu_page('Bestbuy-bestsell Business', 'Bestbuy-bestsell Business', 'manage_options', 'bestbuy-bestsell-business-settings', 'bestbuy_bestsell_business_options',"dashicons-list-view");
    }
    /*
    * Bestbuy-bestsell Business Menu For Admin Panel
    * Render All Menu for Bestbuy-bestsell Business
    */
    function bestbuy_bestsell_business_options(){
        global $current_subtab, $current_tab, $product_id, $product_interest_id, $interest_group_id, $search, $build_subtab, $subtabs, $user_action, $action;

        $bestbuy_bestsell_admin_menu_tab = array( 'payment_method' => 'Payment Method',
            'interest_lists' => 'Interest Lists',
            'interest_groups' => 'Interest Group Lists',
            'interest_confirmed_groups' => 'Interest Confirmed',
            'interest_failed_groups' => 'Failed Campaign',
            'interest_success_groups' => 'Success Campaign' );

        $current_tab     = empty( $_REQUEST['tab'] ) ? 'payment_method' : sanitize_title( $_REQUEST['tab'] );
        $current_subtab = empty( $_REQUEST['subtab'] ) ? '' : sanitize_title( $_REQUEST['subtab'] );
        $product_id = empty( $_REQUEST['product_id'] ) ? '' : sanitize_title( $_REQUEST['product_id'] );
        $search = empty( $_REQUEST['s'] ) ? '' :  $_REQUEST['s'] ;
        $user_action = empty( $_REQUEST['user_action'] ) ? '' :  $_REQUEST['user_action'] ;
        $action = empty( $_REQUEST['action'] ) ? '' :  $_REQUEST['action'] ;
        $interest_group_id = empty( $_REQUEST['group_id'] ) ? '' :  $_REQUEST['group_id'] ;
        $product_interest_id = empty( $_REQUEST['product_interest_id'] ) ? '' :  $_REQUEST['product_interest_id'] ;

        echo '<h2 class="nav-tab-wrapper woo-nav-tab-wrapper">';
        foreach ( $bestbuy_bestsell_admin_menu_tab as $name => $label )
            echo '<a href="' . admin_url( 'admin.php?page=bestbuy-bestsell-business-settings&tab=' . $name ) . '" class="nav-tab ' . ( $current_tab == $name ? 'nav-tab-active' : '' ) . '">' . $label . '</a>';
        echo '</h2>';

        $subtabs = array();
        $subtabs = apply_filters( 'bestbuy_bestsell_business_menu_get_subtab', $subtabs, $current_tab );

        if( isset( $_GET['message'] ) ) {
            $message =  __( 'Your settings have been saved.'  );
            do_action( 'bestbuy_bestsell_show_settings_message' , $message );
            //show_messages( $message );
        }
        //$menu_header_message = show_menu_header_message(  );
        do_action( 'bestbuy_bestsell_show_menu_header_message' );

        if( 'view_interest_details' != $user_action ){
            if( $subtabs ){
                echo '<div><ul class="subsubsub">';
                $array_keys = array_keys( $subtabs );
                foreach ( $subtabs as $id => $label ) {
                    echo '<li><a href="' . admin_url( 'admin.php?page=bestbuy-bestsell-business-settings&tab=' . $current_tab . 		   '&subtab=' . sanitize_title( $id ) ) . '" class="' . ( $current_subtab == $id ? 'current' : '' ) . '">' . $label 			 . '</a> ' . ( end( $array_keys ) == $id ? '' : '|' ) . ' </li>';
                }
                echo '</ul></div><br class="clear" />';
            }
            do_action( 'bestbuy_bestsell_show_menu_lists_title' );
            if( $current_tab ){
                do_action('bestbuy_bestsell_business_menu_section_settings_'.$current_tab, $current_subtab );
            }
        }
    }
    /*
    * Bestbuy-bestsell Business Menu Sub-tab For Admin Panel:
    * Render All Sub-tab for Bestbuy-bestsell Business Admin Menu
    */
    add_filter( 'bestbuy_bestsell_business_menu_get_subtab', 'get_subtabs', 10, 2 );
    function get_subtabs( $subtabs_array, $current_tab ){
        switch( $current_tab ){
            case 'payment_method':
                $subtabs_array['']  =  __( 'PayPal', TEXTDOMAIN );
                $subtabs_array['cash_on_delivery']  = __( 'Cash on Delivery', TEXTDOMAIN );
                $subtabs_array['invoice']  = __( 'Invoice', TEXTDOMAIN );
                break;
            case 'interest_lists':
                $subtabs_array['']  =  __( 'Interest Lists', TEXTDOMAIN );
                break;
            case 'interest_groups':
                $subtabs_array['']  =  __( 'Interest Groups', TEXTDOMAIN );
                break;
            case 'interest_failed_groups':
                $subtabs_array['']  =  __( 'Interest Failed Groups', TEXTDOMAIN );
                break;
            case 'interest_confirmed_groups':
                $subtabs_array['']  =  __( 'Interest Confirmed Groups', TEXTDOMAIN );
                break;
        }
        return $subtabs_array;
    }
    /**
     * Output messages
     * @return string
     */
    add_action( 'bestbuy_bestsell_show_settings_message' , 'show_messages' , 10 , 1 );
    function show_messages( $message ) {
        if( $message ){
            echo '<div id="message" class="updated fade"><p><strong>' . esc_html( $message ) . '</strong></p></div>';
        }
    }
    /*
    * Bestbuy_bestsell Business Menu : Payment Method Tab
    * Render All Sub-tab for Bestbuy_bestsell Business Admin Menu: Payment Method Tab
    */
    add_action( 'bestbuy_bestsell_business_menu_section_settings_payment_method', 'payment_method' ,10, 1 );
    function payment_method( $current_subtab ){
        global $payment_method_form_fields, $payment_method_form_header, $build_subtab ;

        $build_subtab = empty( $current_subtab ) ? 'paypal' : sanitize_title( $current_subtab );
        do_action('bestbuy_bestsell_business_menu_section_payment_method_'.$build_subtab );
        do_action('bestbuy_bestsell_business_menu_section_settings_form_display',  $payment_method_form_fields, $payment_method_form_header );
        do_action( 'bestbuy_bestsell_business_menu_section_settings_payment_method_save_settings', $payment_method_form_fields );
    }
    /*
    * Bestbuy_bestsell Business Menu : Payment Method Tab
    * Render Settings for: PayPal Payment Method
    */
    add_action('bestbuy_bestsell_business_menu_section_payment_method_paypal', 'payment_method_paypal' );
    function payment_method_paypal( ){
        global $payment_method_form_fields, $payment_method_form_header, $options_value, $build_subtab;

        $options_value = get_option( $build_subtab . '_option_settings' );

        $payment_method_form_header = array( 'label'  => __( 'PayPal', TEXTDOMAIN ),
            'description' => __('PayPal Express Checkout works by sending the user to PayPal to enter their payment information.'),
        );
        $payment_method_form_fields = array(
            'enabled' => array(
                'label'   => __( 'Enable/Disable', TEXTDOMAIN ),
                'name'   => $build_subtab.'_enable_disable_payment',
                'id'   => $build_subtab.'_enable_disable_payment',
                'type'    => 'checkbox',
                'description'   => __( 'Enable PayPal Express Checkout', TEXTDOMAIN ),
                'checked' => $options_value[ $build_subtab.'_enable_disable_payment'] ? 'checked': '',
            ),
            'title' => array(
                'label'       => __( 'Title', TEXTDOMAIN ),
                'name'        => $build_subtab.'_checkout_title',
                'id'        => $build_subtab.'_checkout_title',
                'type'        => 'text',
                'value'     => $options_value[ $build_subtab.'_checkout_title'] ? $options_value[$build_subtab.'_checkout_title'] : __( 'PayPal', TEXTDOMAIN ),
                'tooltip_label' => __( 'This controls the title which the user sees during checkout.', TEXTDOMAIN )
            ),
            'description' => array(
                'label'       => __( 'Description', TEXTDOMAIN ),
                'name'        => 'paypal_checkout_description',
                'id'        => 'paypal_checkout_description',
                'type'        => 'text',
                'tooltip_label' => __( 'This controls the description which the user sees during checkout.', TEXTDOMAIN ),
                'value'     => $options_value[ $build_subtab.'_checkout_description'] ? $options_value[$build_subtab.'_checkout_description'] : __( 'Pay via PayPal; you can pay with your credit card if you don&rsquo;t have a PayPal account.', TEXTDOMAIN )
            ),
            'paypal_email' => array(
                'label'       => __( 'PayPal Email', TEXTDOMAIN ),
                'name'        => $build_subtab.'_email',
                'id'        => $build_subtab.'_email',
                'type'        => 'email',
                'tooltip_label' => __( 'Please enter your PayPal email address; this is needed in order to take payment.', TEXTDOMAIN ),
                'value' => $options_value[ $build_subtab.'_email'] ? $options_value[ $build_subtab.'_email'] : '' ,
                'placeholder' => 'you@youremail.com'
            ),
            'paypal_sandbox' => array(
                'label'       => __( 'PayPal sandbox', TEXTDOMAIN ),
                'name'        => $build_subtab.'_sandbox',
                'id'        => $build_subtab.'_sandbox',
                'type'        => 'checkbox',
                'description' => sprintf( __( 'Enable PayPal sandbox. PayPal sandbox can be used to test payments. Sign up for a developer account <a href="%s">here</a>.', TEXTDOMAIN ), 'https://developer.paypal.com/' ),
                'checked' => $options_value[ $build_subtab.'_sandbox'] ? 'checked': '',
            ),
            'shipping_options' => array(
                'label'       => __( 'Shipping options', TEXTDOMAIN ),
                'type'        => 'label',
                'description' => '',
            ),
            'shipping_send' => array(
                'label'       => __( 'Shipping details', TEXTDOMAIN ),
                'name'        => $build_subtab.'_shipping_details_send',
                'id'        => $build_subtab.'_shipping_details_send',
                'type'        => 'checkbox',
                'description' => __( 'Send shipping details to PayPal instead of billing. PayPal allows us to send 1 address. If you are using PayPal for shipping labels you may prefer to send the shipping address rather than billing.', TEXTDOMAIN ),
                'checked' => $options_value[ $build_subtab.'_shipping_details_send'] ? 'checked': '',
            ),
            'address_override' => array(
                'label'       => __( 'Address override', TEXTDOMAIN ),
                'name'        => $build_subtab.'_address_override',
                'id'        => $build_subtab.'_address_override',
                'type'        => 'checkbox',
                'description' => __( 'Enable "address_override" to prevent address information from being changed. PayPal verifies addresses therefore this setting can cause errors (we recommend keeping it disabled).', TEXTDOMAIN ),
                'checked' => $options_value[ $build_subtab.'_address_override'] ? 'checked': '',
            ),
            'advanced_option' => array(
                'label'       => __( 'Advanced options', TEXTDOMAIN ),
                'type'        => 'label',
                'description' => '',
            ),
            'receiver_email' => array(
                'label'       => __( 'Receiver Email', TEXTDOMAIN ),
                'name'        => $build_subtab.'_payment_receiver_email',
                'id'        => $build_subtab.'_payment_receiver_email',
                'type'        => 'email',
                'tooltip_label' => __( 'If your main PayPal email differs from the PayPal email entered above, input your main receiver email for your PayPal account here. This is used to validate IPN requests.', TEXTDOMAIN ),
                'value' => $options_value[ $build_subtab.'_payment_receiver_email'] ? $options_value[$build_subtab.'_payment_receiver_email'] : '' ,
                'placeholder' => 'you@youremail.com'
            ),
            'invoice_prefix' => array(
                'label'       => __( 'Invoice Prefix', TEXTDOMAIN ),
                'name'        => $build_subtab.'_payment_invoice_prefix',
                'id'        => $build_subtab.'_payment_invoice_prefix',
                'type'        => 'text',
                'tooltip_label' => __( 'Please enter a prefix for your invoice numbers. If you use your PayPal account for multiple stores ensure this prefix is unique as PayPal will not allow orders with the same invoice number.', TEXTDOMAIN ),
                'value' => $options_value[ $build_subtab.'_payment_invoice_prefix'] ? $options_value[$build_subtab.'_payment_invoice_prefix'] : 'inmid-' ,
            ),
            'api_credentials' => array(
                'label'       => __( 'API Credentials', TEXTDOMAIN ),
                'type'        => 'label',
                'description' => sprintf( __( 'Enter your PayPal API credentials to process refunds via PayPal. Learn how to access your PayPal API Credentials %shere%s.', TEXTDOMAIN ), '<a href="https://developer.paypal.com/webapps/developer/docs/classic/api/apiCredentials/#creating-classic-api-credentials">', '</a>' ),
            ),
            'api_username' => array(
                'label'       => __( 'API Username', TEXTDOMAIN ),
                'name'        => $build_subtab.'_payment_api_user_name',
                'id'        => $build_subtab.'_payment_api_user_name',
                'type'        => 'text',
                'tooltip_label' => __( 'Get your API credentials from PayPal.', TEXTDOMAIN ),
                'value' => $options_value[ $build_subtab.'_payment_api_user_name'] ? $options_value[$build_subtab.'_payment_api_user_name'] : '' ,
                'placeholder' => __( 'Optional', TEXTDOMAIN )
            ),
            'api_password' => array(
                'label'       => __( 'API Password', TEXTDOMAIN ),
                'name'        => $build_subtab.'_payment_api_password',
                'id'        => $build_subtab.'_payment_api_password',
                'type'        => 'text',
                'tooltip_label' => __( 'Get your API credentials from PayPal.', TEXTDOMAIN ),
                'value' => $options_value[ $build_subtab.'_payment_api_password'] ? $options_value[$build_subtab.'_payment_api_password'] : '' ,
                'placeholder' => __( 'Optional', TEXTDOMAIN )
            ),
            'api_signature' => array(
                'label'       => __( 'API Signature', TEXTDOMAIN ),
                'name'        => $build_subtab.'_payment_api_signature',
                'id'        => $build_subtab.'_payment_api_signature',
                'type'        => 'text',
                'tooltip_label' => __( 'Get your API credentials from PayPal.', TEXTDOMAIN ),
                'value' => $options_value[ $build_subtab.'_payment_api_signature'] ? $options_value[$build_subtab.'_payment_api_signature'] : '' ,
                'placeholder' => __( 'Optional', TEXTDOMAIN )
            ),
            'submit_button' => array(
                'label'       => __( 'Save changes', TEXTDOMAIN ),
                'name'        => 'paypal_payment_settings',
                'id'        => 'paypal_payment_settings',
                'type'        => 'submit_button',
            ),
        );
    }
    /*
    * Bestbuy_bestsell Business Menu : Payment Method Tab
    * Render Settings for: Cash On Delivery Payment Method
    */
    add_action('bestbuy_bestsell_business_menu_section_payment_method_cash_on_delivery', 'payment_method_cash_on_delivery' );
    function payment_method_cash_on_delivery( ){
        global $payment_method_form_fields, $payment_method_form_header, $options_value, $build_subtab;

        $options_value = get_option( $build_subtab . '_option_settings' );

        $payment_method_form_header = array( 'label'  => __( 'Cash on Delivery', TEXTDOMAIN ),
            'description' => __('Cash on Delivery works by sending the user goods/products and get cash payment.'),
        );
        $payment_method_form_fields = array(
            'enabled' => array(
                'label'   => __( 'Enable/Disable', TEXTDOMAIN ),
                'name'   => $build_subtab.'_enable_disable',
                'id'   => $build_subtab.'_enable_disable',
                'type'    => 'checkbox',
                'description'   => __( 'Enable Cash on Delivery', TEXTDOMAIN ),
                'checked' => $options_value[ $build_subtab.'_enable_disable'] ? 'checked': '',
            ),
            'title' => array(
                'label'       => __( 'Title', TEXTDOMAIN ),
                'name'        => $build_subtab.'_title',
                'id'        => $build_subtab.'_title',
                'type'        => 'text',
                'value'     => $options_value[$build_subtab.'_title'] ? $options_value[$build_subtab.'_title'] : __( 'Cash on Delivery', TEXTDOMAIN ),
                'tooltip_label' => __( 'This controls the title which the user sees during checkout.', TEXTDOMAIN )
            ),
            'submit_button' => array(
                'label'       => __( 'Save changes', TEXTDOMAIN ),
                'name'        => 'cash_on_delivery_settings',
                'id'        => 'cash_on_delivery_settings',
                'type'        => 'submit_button',
            ),
        );
    }
    /*
    * Bestbuy_bestsell Business Menu : Payment Method Tab
    * Render Settings for: Invoice Payment Method
    */
    add_action('bestbuy_bestsell_business_menu_section_payment_method_invoice', 'payment_method_invoice' );
    function payment_method_invoice(  ){
        global $payment_method_form_fields, $payment_method_form_header, $options_value, $build_subtab;

        $options_value = get_option( $build_subtab . '_option_settings' );

        $payment_method_form_header = array( 'label'  => __( 'Invoice', TEXTDOMAIN ),
            'description' => __('Invoice works by sending the user E-invoice to their E-mail'),
        );
        $payment_method_form_fields = array(
            'enabled' => array(
                'label'   => __( 'Enable/Disable', TEXTDOMAIN ),
                'name'   => $build_subtab.'_enable_disable',
                'id'   => $build_subtab.'_enable_disable',
                'type'    => 'checkbox',
                'description'   => __( 'Enable Invoice', TEXTDOMAIN ),
                'checked' => $options_value[ $build_subtab.'_enable_disable'] ? 'checked': '',
            ),
            'title' => array(
                'label'       => __( 'Title', TEXTDOMAIN ),
                'name'        => $build_subtab.'_title',
                'id'        => $build_subtab.'_title',
                'type'        => 'text',
                'value'     => $options_value[ $build_subtab.'_title'] ? $options_value[$build_subtab.'_title'] : __( 'Invoice', TEXTDOMAIN ),
                'tooltip_label' => __( 'This controls the title which the user sees during checkout.', TEXTDOMAIN )
            ),
            'submit_button' => array(
                'label'       => __( 'Save changes', TEXTDOMAIN ),
                'name'        => 'invoice_settings',
                'id'        => 'invoice_settings',
                'type'        => 'submit_button',
            ),
        );
    }
    /*
    * Bestbuy_bestsell Business Menu : Payment Method Tab
    * Render Settings Form for : All types of payment method
    */
    add_action( 'bestbuy_bestsell_business_menu_section_settings_form_display', 'section_settings_form_display' , 10, 2 );
    function section_settings_form_display( $options , $form_header){
        if( $form_header ){
            echo '<h3>'. $form_header['label'] .'</h3>';
            echo '<p>'. $form_header['description'] .'</p>';
        }
        if( $options ){
            echo '<form method="post" enctype="multipart/form-data" id="save_payment_settings_form">';
            echo '<table class="form-table"><tbody>';
            foreach( $options as $field ) {
                echo '<tr valign="top">';
                switch ( $field['type'] ) {
                    case 'label':
                        echo '<th class="titledesc" scope="row"><label >'.$field['label'].'</label></th><td></td>';
                        break;
                    case 'checkbox':
                        echo '<th class="titledesc" scope="row"><label><strong>'.$field['label'].'</strong></label>'
                            .get_tooltip_html( $field ).
                            '</th>
            <td class="forminp"><input '. $field['checked'].' type="checkbox" name="'. $field['name']. '" id="'. $field['id']. '"  />&nbsp;'.$field['description']."</td>";
                        break;
                    case 'text': // The html to display for the text type
                        echo '<th class="titledesc" scope="row"><label><strong>'.$field['label'].'</strong></label>'
                            .get_tooltip_html( $field ).
                            '</th>
            <td class="forminp">';
                        echo '<input '. $field['disabled']. ' type="text" name="'. $field['name']. '" id="'. $field['id']. '" value="'.$field['value']. '" ' .'" placeholder="'.$field['placeholder']. '" ' . $field['attribute'] .'/>'."</td>";
                        break;
                    case 'email': // The html to display for the text type
                        echo '<th class="titledesc" scope="row"><label><strong>'.$field['label'].'</strong></label>'
                            .get_tooltip_html( $field ).
                            '</th>
            <td class="forminp">';
                        echo '<input '. $field['disabled']. ' type="email" name="'. $field['name']. '" id="'. $field['id']. '" value="'.$field['value']. '" ' .'" placeholder="'.$field['placeholder']. '" ' . $field['attribute'] .' class="requiredField" />'."</td>";
                        break;
                    case 'select': // The html to display for the text type
                        echo '<th class="titledesc" scope="row"><label><strong>'.$field['label'].'</strong></label>'
                            .get_tooltip_html( $field ).
                            '</th>
            <td class="forminp">';
                        echo '<select '. $field['disabled']. ' name="'. $field['name']. '" id="'. $field['id']. '" >';
                        foreach( $field['options'] as $each_option ){
                            if( isset( $product_data[ $field['name'] ] ) && $_POST[ $field['name'] ]===$each_option['value'] ){
                                $each_option['selected'] = 'selected';
                            }
                            echo '<option '. $each_option['selected'].'  value="'.$each_option['value']. '" >' . $each_option['label']. '</option>';
                        }
                        echo '</select ></td>';
                        break;
                    case 'textarea': // The html to display for the textarea type
                        echo '<th class="titledesc" scope="row"><label><strong>'.$field['label'].'</strong></label>'
                            .get_tooltip_html( $field ).
                            '</th>
            <td class="forminp">';
                        echo '<textarea name="'. $field['name']. '" id="'.$field['id']. '"placeholder="'. $field['placeholder']. '">'. $field['value']. '</textarea></td>';       						break;
                    case 'texteditor':
                        echo '<th class="titledesc" scope="row"><label><strong>'.$field['label'].'</strong></label>'
                            .get_tooltip_html( $field ).
                            '</th><td class="forminp">';
                        wp_editor( $field['value'] , 'product-textarea', $field['settings']);
                        echo '</td>';
                        break;
                }
                if( $field['type'] === 'submit_button' ){
                    echo '<th class="titledesc" scope="row"></th><td class="forminp">';
                    echo '<input name="'.$field['name'].'" class="button-primary" type="submit" value="'.$field['label'].'" /></td>';
                }
            }
            echo '</tr></tbody></table>';
            wp_nonce_field( );
            echo '</form>';
        }
    }
    /**
     * Get HTML for tooltips
     *
     * @param  array $data
     * @return string
     */
    function get_tooltip_html( $data ) {
        if ( isset( $data['tooltip_label'] )) {
            $tip = $data['tooltip_label'];
        }
        return $tip ? '<a title="' . esc_attr( $tip ) . '" > <img class="help_tip" data-tip="' . esc_attr( $tip ) . '" src="' . BESTBUY_BESTSELL_DIR . '/images/help.png" height="16" width="16" /></a>' : '';
    }
    /*
    * Bestbuy-bestsell Business Menu : Payment Method Tab
    * Save Settings for : All types of payment method
    */
    add_action('bestbuy_bestsell_business_menu_section_settings_payment_method_save_settings', 'payment_method_save_settings',10, 1 );
    function payment_method_save_settings( $form_fields ){
        global $sanitize_post_data, $messages, $build_subtab;
        $messages = array();
        $sanitize_post_data = array();
        if(	isset( $_POST[ $form_fields['submit_button']['name'] ] ) ){
            validate_settings_fields( $form_fields, '' );
            $success = update_option( $build_subtab . '_option_settings',  $sanitize_post_data );
        }
        //print_r( $sanitize_post_data ); exit;
        //echo $build_subtab; exit;
        $options_value = get_option( $build_subtab . '_option_settings' );
        //print_r( $options_value ); exit;
        if( $success ){
            $redirect_to = admin_url().' admin.php?page=bestbuy-bestsell-business-settings&tab=payment_method&subtab='.$_GET['subtab'].'&message='. $success;
            wp_redirect( $redirect_to );
        }
    }
    /**
     * Validate Settings Field Data.
     *
     * Validate the data on the "Settings" form / Product
     *
     * @since 1.0.0
     * @uses method_exists()
     * @param bool $form_fields (default: false), $form_type ( default: empty )
     */
    function validate_settings_fields( $form_fields, $form_type  ) {
        global $sanitize_post_data, $build_subtab;

        if ( ! $form_fields && empty( $form_type ) ) {
            do_action('inmid_business_menu_section_payment_method_'.$build_subtab);
        }elseif ( ! $form_fields &&  'product_interest_header_form' === $form_type ) {
            do_action( 'inmid_group_price_form_settings' );
        }
        if( $form_fields ){

            foreach ( $form_fields as $key => $value ) {
                if ( empty( $value['type'] ) ) {
                    $value['type'] = 'text'; // Default to "text" field type.
                }
                // Look for a validate_FIELDID_field method for special handling
                if ( function_exists( 'validate_' . $key . '_field' ) ) {
                    $function_call = 'validate_' . $key . '_field';
                    $field = $function_call ( $value , $form_type );
                    $sanitize_post_data[ $value['name'] ] = $field;

                    // Look for a validate_FIELDTYPE_field method
                } elseif ( function_exists( 'validate_' . $value['type'] . '_field' ) ) {
                    $function_call = 'validate_' . $value['type'] . '_field';
                    $field = $function_call ( $value, $form_type );
                    $sanitize_post_data[ $value['name'] ] = $field;
                    // Default to text
                } else {
                    $field = validate_text_field( $value, $form_type );
                    $sanitize_post_data[ $value['name'] ] = $field;
                }
            }
        }
    }
    /**
     * Validate Checkbox Field.
     *
     * If not set, return "no", otherwise return "yes".
     *
     * Param mixed $key, $form_type ( default: empty )
     * @since 1.0.0
     * @return string
     */
    function validate_checkbox_field( $value, $form_type ) {
        $status = '';
        if ( isset( $_POST[ $value['name'] ] ) && ( 'on' == $_POST[ $value['name'] ] ) ) {
            $status = 'yes';
        }
        return $status;
    }
    /**
     * Validate Text Field.
     *
     * Make sure the data is escaped correctly, etc.
     *
     * @param mixed $value, $form_type ( default: empty )
     * @return string
     */
    function validate_text_field( $value, $form_type ) {

        if( empty( $form_type ) ){
            $text = get_option( $value['name'] );
        }

        if ( isset( $_POST[ $value['name'] ] ) ) {
            $text = wp_kses_post( trim( stripslashes( $_POST[ $value['name'] ] ) ) );
        }
        return $text;
    }
    /**
     * Validate Select Field.
     *
     * Make sure the data is escaped correctly, etc.
     *
     * Param mixed $key, $form_type ( default: empty )
     * @since 1.0.0
     * @return string
     */
    function validate_select_field( $value , $form_type ) {

        if( empty( $form_type ) ){
            $text = get_option( $value['name'] );
        }
        if ( isset( $_POST[ $value['name'] ] ) ) {
            $value = stripslashes( $_POST[ $value['name'] ]  );
        }
        return $value;
    }
    /**
     * Bestbuy-bestsell Interest Lists Section
     *
     * @param $current_subtab
     * @return Bestbuy-bestsell Interest Lists Section
     */
    add_action( 'bestbuy_bestsell_business_menu_section_settings_interest_lists', 'bestbuy_bestsell_interest_lists_section' ,10, 1 );
    function bestbuy_bestsell_interest_lists_section( $current_subtab ){
        global $build_subtab;
        $build_subtab = empty( $current_subtab ) ? 'interest_lists' : sanitize_title( $current_subtab );
        do_action('bestbuy_bestsell_business_menu_section_interest_lists_'.$build_subtab, $build_subtab );
        //do_action('inmid_business_menu_section_settings_form_display',  $payment_method_form_fields, $payment_method_form_header );
        //do_action( 'inmid_business_menu_section_settings_payment_method_save_settings', $payment_method_form_fields, $build_subtab );
    }
    /**
     * Bestbuy-bestsell Interest Lists:  Group By Product
     *
     * @param $current_subtab
     * @return Interst Lists: Group By Product
     */
    add_action('bestbuy_bestsell_business_menu_section_interest_lists_interest_lists', 'bestbuy_bestsell_interest_lists', 1 );
    function bestbuy_bestsell_interest_lists( $build_subtab ){
        global $user_action;
        $bestbuy_bestsell_interest_list_object = new Bestbuybestsell_Interest();

        switch( $user_action ){
            case 'product-interest-lists':
                $search_text = __( 'Search Interests' );
                break;
            default:
                $search_text = __( 'Search Products' ) ;
        }
        echo '<form method="post" id="bestbuy_bestsell_interest">';
        $bestbuy_bestsell_interest_list_object->prepare_interest_lists_items( );
        $bestbuy_bestsell_interest_list_object->search_box( $search_text , 'product_interest_search' );
        //$this->display();
        $bestbuy_bestsell_interest_list_object->display();
        //echo '<input type="hidden" name="action" value="'. $inmid_interest_list_object->current_action(). '" />';
        echo '</form>';
    }
    /**
     * Show menu header message for Bestbuy_bestsell Admin Menu
     *
     */
    add_action( 'bestbuy_bestsell_show_menu_header_message', 'show_menu_header_message', 10 );
    function show_menu_header_message( ){
        global $current_subtab, $current_tab, $product_id, $interest_group_id, $group_info, $group_details, $search, $build_subtab, $subtabs, $sql_posts_total, $product_interest_lists, $group_details, $show_description_header, $user_action ;
        $show_description_header = FALSE;
        $show_interest_details_section = FALSE;
        $bestbuy_bestsell_interest_list_object = new Bestbuybestsell_Interest();

        $product_interest_lists = $bestbuy_bestsell_interest_list_object ->get_product_interest_lists( $per_page = 5 , $page_number = 1, 'product_details' );
        if( 'product-interest-lists' != $user_action ){
            $group_info = $bestbuy_bestsell_interest_list_object ->get_group_info( $interest_group_id );
            $group_details = $bestbuy_bestsell_interest_list_object ->get_interest_group_details( $per_page = 5, $page_number = 1 , $interest_group_id, $group_price_id , 'group_details');
        }
        switch( $current_tab ){
            case 'interest_lists':
                if( empty( $current_subtab ) && empty( $user_action ) ){
                    $header_message = __('Step 1 : Interest Lists');
                }
                elseif( isset( $user_action ) && 'product-interest-lists' === $user_action ){
                    $header_message = __('Step 1.1 : Interest List For ');
                    $header_message .= '" '.get_the_title( $product_id ).' "';
                    $show_description_header = TRUE;
                }
                elseif( isset( $user_action ) && 'view_interest_details' === $user_action ){
                    $header_message = __('Interest Details For ');
                    $header_message .= '" '.get_the_title( $product_id ).' "';
                    $show_interest_details_section = TRUE;
                }
                break;
            case 'interest_groups':
                if( empty( $current_subtab ) && empty( $user_action ) ){
                    $header_message = __('Step 2 : Interest Groups ');
                }
                elseif( isset( $user_action ) && 'view-group-details' === $user_action ){
                    $header_message = __('Step 2.1 : Interest Group For ');
                    $header_message .= '" '.get_the_title( $product_id ).' "';
                    $show_description_header = TRUE;
                }
                elseif( isset( $user_action ) && 'set-group-price' === $user_action ){
                    $header_message = __('Step 2.2 : Set Price For ');
                    $header_message .= '" '.get_the_title( $product_id ).' "';
                    $show_description_header = TRUE;
                }
                elseif( isset( $user_action ) && 'add-more-interests' === $user_action ){
                    $header_message = __('Add To Group : Add More Interest To ');
                    $header_message .= '" '.$group_info[0]['group_name'].' "';
                    $show_description_header = TRUE;
                }
                break;
            case 'interest_confirmed_groups':
                if( empty( $current_subtab ) && empty( $user_action ) ){
                    $header_message = __('Step 3 : Confirmed Interest Groups ');
                }elseif( isset( $user_action ) && 'view-confirmed-group-details' === $user_action ){
                    $header_message = __('Step 3.1 : Confirmed Interest Group For ');
                    $header_message .= '" '.get_the_title( $product_id ).' "';
                    $show_description_header = TRUE;
                }elseif( isset( $user_action ) && 'add-more-interests-to-confirmed-group' === $user_action ){
                    $header_message = __('Add To Confirmed Group : Add More Interest To ');
                    $header_message .= '" '.$group_info[0]['group_name'].' "';
                    $show_description_header = TRUE;
                }
                break;
            case 'interest_failed_groups':
                if( empty( $current_subtab ) && empty( $user_action ) ){
                    $header_message = __('Step 5 : Failed Interest Groups ');
                }elseif( isset( $user_action ) && 'view-failed-group-details' === $user_action ){
                    $header_message = __('Step 5.1 : Failed Interest Group For ');
                    $header_message .= '" '.get_the_title( $product_id ).' "';
                    $show_description_header = TRUE;
                }
                break;
        }
        echo '<div class="campaign_step" > <h2 >'. $header_message . '</h2> </div><br class="clear" />';
        if( $show_description_header ){
            do_action( 'bestbuy_bestsell_show_product_interest_description_header' );
        }
        if( $show_interest_details_section ){
            do_action( 'bestbuy_bestsell_show_product_interest_details_section' );
        }
    }

    ////////////////////////////////////////////////////////////
    /**
     * Show show_product_interest_details_section for a Particular Interest
     * Show Product Title, Product Thumb , No of Interest, No of Interester, Group Name, Group Closing Date, */
    add_action( 'bestbuy_bestsell_show_product_interest_details_section' , 'show_product_interest_details_section' , 5 );
    function show_product_interest_details_section( ){
        global $product_interest_id, $user_product_interest_details;
        $bestbuy_bestsell_interest_details_object = new Bestbuybestsell_Interest();
        $user_product_interest_details = $bestbuy_bestsell_interest_details_object ->user_product_interest_details( $product_interest_id );
    }

    /**
     *Show show_product_interest_details_section for a Particular Interest
     * Show Product Title, Product Thumb , No of Interest, No of Interester, Group Name, Group Closing Date, */
    add_action( 'bestbuy_bestsell_show_product_interest_details_section' , 'show_product_interest_details_section_interest_details' , 10 );
    function show_product_interest_details_section_interest_details( ){
        global $product_interest_id, $user_product_interest_details;
        $bestbuy_bestsell_interest_details_object = new Bestbuybestsell_Interest();
        ?>
        <div class="product_interest_description_header">
        <h2><?php _e('Interest Details'); ?> </h2>
        <?php
        if( $user_product_interest_details ){
            $post_thumbnail = get_the_post_thumbnail( $user_product_interest_details[0]['ID'], 'medium' );
            $interest_meta_details = $bestbuy_bestsell_interest_details_object ->get_user_product_interest_meta( $product_interest_id );
            ?>
            <div class="interest_details">
                <div class="product_thumb">
                    <?php echo $post_thumbnail."<br/>";
                    if( $user_product_interest_details[0]['post_status']!='publish' ){ ?>
                    <span class="product_status">
					<?php  _e('Waiting for approval');
                    }
                    ?> </span>
                </div>
                <div class="interest_info">
				<span class="product_title">
					<?php echo $user_product_interest_details[0]['post_title']." ( ". $user_product_interest_details[0]['interest_qty']." Pc"; if( $user_product_interest_details[0]['interest_qty'] > 1 ){ echo "s";} echo " )"; ?>
				</span><br/><br/>
                    <?php
                    ////////////////////////////////////////////////
                    $interest_meta_array = array();
                    if( $interest_meta_details ) {
                        foreach( $interest_meta_details as $interest_meta_data ) {
                            if( $interest_meta_data ){
                                $interest_meta_array[$interest_meta_data['meta_name']] = $interest_meta_data['meta_value'];     }
                        }
                        $submit_btn_name = "interest_update";
                        $submit_btn_value = "Update";
                    }else{ ?>
                        <span class="meta_not_available">
						<?php
                        _e("Please Choose Product Attributes ");
                        $submit_btn_name = "interest_assign";
                        $submit_btn_value = "Save";
                        ?>
					</span>
                    <?php }
                    if( $user_product_interest_details[0]['post_status'] ==='publish' ){
                        if( isset( $_SESSION['interest_assign_success'] ) ){
                            echo '<p class="sucess_messages">';
                            _e("Attributes Saved Successfully");
                            unset( $_SESSION['interest_assign_success'] ) ;
                            echo '</p>';
                        }
                        $product_attributes = get_field('attributes', $user_product_interest_details[0]['ID']);
                        if( $product_attributes ) {
                            ?>
                            <div class="interest_assign">
                                <form method="post" name="interest_assign">
                                    <div style="margin:0 0 10px 0; float:left;">
                                        <?php
                                        $num_cloms = 0;
                                        foreach( $product_attributes as $product_attribute ) {
                                            if($product_attribute){ $num_cloms++; ?>
                                                <div class="product_attribute_div" style="margin:0 20px 10px 0; padding-right:5px; float:left;  background: none repeat-x scroll 0 0;">
                                                    <?php 		///$interest_meta_array = array();
                                                    echo '<h4>'.$product_attribute['label'].'</h4>';
                                                    if($product_attribute['values']){
                                                        foreach($product_attribute['values'] as $value) {
                                                            ?>
                                                            <label><input type="radio" name="<?php echo $product_attribute['label']; ?>" value="<?php echo $value['value']; ?>"
                                                                    <?php if( strtolower( $interest_meta_array[ $product_attribute["label"] ] )  == strtolower( $value['value'] ) ){ ?> checked=checked <?php } ?> > <?php echo $value['value']; ?> </label> <br />						     <?php
                                                        }
                                                    }?>
                                                </div>
                                                <?php if($num_cloms==3){ $num_cloms = 0; ?> <div style="clear:both;"> </div> <?php }
                                            }
                                        }
                                        ?>
                                        <input type="hidden" name="product_interest_id" value="<?php echo $product_interest_id; ?>" />
                                        <input type="hidden" name="user_id" value="<?php echo $user_product_interest_details[0]['user_id']; ?>" />
                                        <input type="hidden" name="product_id" value="<?php echo $user_product_interest_details[0]['product_id']; ?>" />
                                        <div class="submit_btn"> <input type="submit" name="<?php echo $submit_btn_name; ?>" value="<?php echo $submit_btn_value;?>" /></div>
                                    </div>
                                </form>
                            </div>
                        <?php } } ?>
                    <div style="clear:both;"> </div>
                    <span class="start-date_label"> <?php _e("Interest Start"); ?> : </span>
					<span class="start-date">
						<?php if( $user_product_interest_details[0]['interest_start_date'] ){
                            echo date( "Y-m-d", $user_product_interest_details[0]['interest_start_date'] ); }
                        elseif( $user_product_interest_details[0]['asa_price_is_reasonable'] ){ _e("As soon as price is reasonable"); } ?>
					</span>
                    <?php if( $user_product_interest_details[0]['interest_end_date'] ){ ?>
                        <br/><span class="end-date_label"> <?php _e("Interest End"); ?> : </span>
                        <span class="start-date"> <?php echo date( "Y-m-d", $user_product_interest_details[0]['interest_end_date'] );  ?></span>
                    <?php } ?>
                    <?php if( $user_product_interest_details[0]['interest_recuring_purchase'] ){ ?>
                        <br/><span class="end-date_label"> <?php _e("Re-curing Purchase"); ?> : </span>
                        <span class="start-date">
							<?php echo $user_product_interest_details[0]['interest_recuring_purchase'];  ?></span>
                    <?php } ?>
                    <?php if( $user_product_interest_details[0]['interest_notes'] ){ ?>
                        <br/><span class="end-date_label"> <?php _e(" Interest Notes"); ?> : </span>
                        <span class="start-date"> <?php echo $user_product_interest_details[0]['interest_notes'];  ?></span>
                    <?php } ?>
                </div>
            </div><br class="clear">
            </div>
            <?php
        }
    }
    /**
     * Show show_product_interest_details_section for a Particular Interest
     * Show Product Title, Product Thumb , No of Interest, No of Interester, Group Name, Group Closing Date,
     */
    add_action( 'bestbuy_bestsell_show_product_interest_details_section' , 'show_product_interest_details_section_interest_by_details' , 15 );
    function show_product_interest_details_section_interest_by_details( ){
        global $product_interest_id, $user_product_interest_details, $interest_by_details ;

        ?>
        <div class="interest_by">
            <h2> <?php _e("Interest By"); ?> : </h2>
            <div class="interest_by_thumb"> <?php echo get_avatar( $user_product_interest_details[0]['user_id']	, "302", "", "Not Available" ); ?> </div>
        </div>
        <div class="interest_by_info">
        <?php
        ////////////////////////////////////////////////
        $interest_by_details = get_userdata( $user_product_interest_details[0]['user_id'] );
        $interest_by_meta = get_user_meta( $user_product_interest_details[0]['user_id'] );
        $user_id = $user_product_interest_details[0]['user_id'];
        //print_r($interest_by_meta); exit;
        if( $interest_by_details ) { ?>
            <span class="user_name">
					<a href="user-edit.php?user_id=<?php echo $user_product_interest_details[0]['user_id']; ?>">
                        <?php
                        echo $interest_by_details->display_name;
                        if( $interest_by_details->roles ){
                            echo " ( ".$current_user_role = implode(', ', $interest_by_details->roles)." )" ;
                        }
                        ?>
                    </a>
					</span>
            <?php if( $interest_by_meta['first_name'][0] || $interest_by_meta['last_name'][0] ){ ?>
                <br/><br/><span class="interest_by_name_label"> <?php _e("Name"); ?> : </span>
                <span class="person_name"> <?php echo $interest_by_meta['first_name'][0]? $interest_by_meta['first_name'][0]: "";  echo $interest_by_meta['last_name'][0]? "&nbsp;".$interest_by_meta['last_name'][0]: ""; ?></span>
            <?php }
            if( $interest_by_details->user_email ){ ?>
                <br/><span class="interest_by_name_label"> <?php _e("E-mail"); ?> : </span>
                <span class="person_name"> <?php echo $interest_by_details->user_email; ?></span>
            <?php }
            if( $interest_by_meta['phone'][0] ){ ?>
                <br/><span class="interest_by_name_label"> <?php _e("Phone"); ?> : </span>
                <span class="person_name"> <?php echo $interest_by_meta['phone'][0]; ?></span>
            <?php }
            if( $interest_by_meta['user_country'][0] ){ ?>
                <br/><span class="interest_by_name_label"> <?php _e("Country"); ?> : </span>
                <span class="person_name"> <?php echo $interest_by_meta['user_country'][0]; ?></span>
            <?php }
            if( $interest_by_meta['city'][0] ){ ?>
                <br/><span class="interest_by_name_label"> <?php _e("City"); ?> : </span>
                <span class="person_name"> <?php echo $interest_by_meta['city'][0]; ?></span>
            <?php }
            if( $user_product_interest_details[0]['authorative_person'] ){ ?>
                <br/><span class="authoritative_person_info"> <?php _e("Authoritative Person Info"); ?> : </span><br/><span class="interest_by_name_label"> <?php _e("Name"); ?> : </span>
                <span class="person_name"> <?php echo $user_product_interest_details[0]['authorative_person_first_name']."&nbsp;".$user_product_interest_details[0]['authorative_person_last_name']; ?></span><br/><span class="interest_by_name_label"> <?php _e("E-mail"); ?> : </span>
                <span class="person_name"> <?php echo $user_product_interest_details[0]['authorative_person_email']; ?></span><br/><span class="interest_by_name_label"> <?php _e("Phone"); ?> : </span><span class="person_name"> <?php echo $user_product_interest_details[0]['authorative_person_phone']; ?></span>
            <?php }
            ?>
            </div>
        <?php }else{ ?>
            <span class="user_name"> <?php _e("Visitor"); ?></span>
            <br/><br/><span class="interest_by_name_label"> <?php _e("E-mail"); ?> : </span>
            <span class="person_name"> <?php echo $user_product_interest_details[0]['interest_visitor_email']; ?></span><br/><span class="interest_by_name_label"> <?php _e("Phone"); ?> : </span><span class="person_name"> <?php echo $user_product_interest_details[0]['interest_visitor_phone']; ?></span>
        <?php }

    }
    /**
     *Show show_product_interest_details_section for a Particular Interest
     * Show Product Title, Product Thumb , No of Interest, No of Interester, Group Name, Group Closing Date,
     */
    add_action( 'bestbuy_bestsell_show_product_interest_details_section' , 'show_product_interest_details_section_interest_shipping_details' , 20 );
    function show_product_interest_details_section_interest_shipping_details( ){
        global $product_interest_id, $user_product_interest_details, $interest_by_details ;

        ?>
        <div class="shipping_address" >
        <?php
        if( $interest_by_details ) { ?>
            <br/><span class="authoritative_person_info"> <?php _e("Shipping Address"); ?> : </span><br/>
            <?php
            $address = '';
            $address .= get_user_meta( $user_id, 'shipping_first_name', true );
            $address .= ' ';
            $address .= get_user_meta( $user_id, 'shipping_last_name', true );
            $address .= "<br/>";
            $address .= get_user_meta( $user_id, 'shipping_company', true );
            $address .= "<br/>";
            $address .= get_user_meta( $user_id, 'shipping_address_1', true );
            $address .= "<br/>";
            $address .= get_user_meta( $user_id, 'shipping_address_2', true );
            $address .= "<br/>";
            $address .= get_user_meta( $user_id, 'shipping_city', true );
            $address .= "<br/>";
            $address .= get_user_meta( $user_id, 'shipping_state', true );
            $address .= "<br/>";
            $address .= get_user_meta( $user_id, 'shipping_postcode', true );
            $address .= "<br/>";
            $address .= get_user_meta( $user_id, 'shipping_country', true );
            echo $address;
            ?>
            <br/><span class="authoritative_person_info"> <?php _e("Billing Address"); ?> : </span><br/>
            <?php
            $address = '';
            $address .= get_user_meta( $user_id, 'billing_first_name', true );
            $address .= ' ';
            $address .= get_user_meta( $user_id, 'billing_last_name', true );
            $address .= "<br/>";
            $address .= get_user_meta( $user_id, 'billing_company', true );
            $address .= "<br/>";
            $address .= get_user_meta( $user_id, 'billing_address_1', true );
            $address .= "<br/>";
            $address .= get_user_meta( $user_id, 'billing_address_2', true );
            $address .= "<br/>";
            $address .= get_user_meta( $user_id, 'billing_city', true );
            $address .= "<br/>";
            $address .= get_user_meta( $user_id, 'billing_state', true );
            $address .= "<br/>";
            $address .= get_user_meta( $user_id, 'billing_postcode', true );
            $address .= "<br/>";
            $address .= get_user_meta( $user_id, 'billing_country', true );
            echo $address;
            ?>
            </div>

            <?php
        }
        ?>
        <br class="clear">
        <?Php
    }

    /**
     *Show show_product_interest_details_section for a Particular Interest
     * Show Product Title, Product Thumb , No of Interest, No of Interester, Group Name, Group Closing Date,
     */
    add_action( 'bestbuy_bestsell_show_product_interest_details_section' , 'show_product_interest_details_section_interest_assign_or_update' , 25 );
    function show_product_interest_details_section_interest_assign_or_update( ){
        global $product_id, $product_interest_id, $user_product_interest_details, $interest_by_details ;

        if( isset( $_REQUEST['interest_assign'] ) || isset( $_REQUEST['interest_update'] ) ){ //Color: Red | Cover: Strong | Paper-size: A4| Printing: both page | Printing-from: press
            //$interest_assign_data = $_REQUEST['interest_notes'];
            $bestbuy_bestsell_interest_details_object = new Bestbuybestsell_Interest();
            $interest_id = $_REQUEST['product_interest_id'];
            $product_id = $_REQUEST['product_id'];
            $user_id = $_REQUEST['user_id'];
            $wp_product_interest_data['product_id'] = $product_id;
            $wp_product_interest_data['user_id'] = $user_id;

            $product_attributes = get_field('attributes', $product_id);
            if( $product_attributes ) {
                foreach($product_attributes as $product_attribute) {
                    $interest_assign_data[] = array('name' => $product_attribute['label'], 'value' => $_POST[$product_attribute['label']]);
                }
            }	//print_r($interest_assign_data); exit;
            if( $interest_assign_data ){
                if( isset( $_REQUEST['interest_assign'] ) ){
                    $success = $bestbuy_bestsell_interest_details_object ->wp_product_interest_meta_insert( $interest_id, $wp_product_interest_data, $interest_assign_data );
                    $_SESSION['interest_assign_success'] = $success;
                }
                if( isset( $_REQUEST['interest_update'] ) ){
                    $success = $bestbuy_bestsell_interest_details_object ->wp_product_interest_meta_update( $interest_id, $wp_product_interest_data, $interest_assign_data );
                    $_SESSION['interest_assign_success'] = $success;
                }
            }
            wp_safe_redirect(  add_query_arg( )  );
        }
    }
    ////////////////////////////////////////////////////////////

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
    add_filter ("wp_mail_content_type", "bestbuy_bestsell_mail_content_type");
    function bestbuy_bestsell_mail_content_type() {
        return "text/html";
    }

    add_filter ("wp_mail_from", "bestbuy_bestsell_mail_from");
    function bestbuy_bestsell_mail_from() {
        return "info@logic-coder.info";
    }

    add_filter ("wp_mail_from_name", "bestbuy_bestsell_mail_from_name");
    function bestbuy_bestsell_email_from_name() {
        return "Bestbuy-bestsell";
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
        $results_interest = $wpdb->get_results( " SELECT * FROM {$wpdb->prefix}users, {$wpdb->prefix}product_interest, {$wpdb->prefix}posts WHERE {$wpdb->prefix}users.ID = {$wpdb->prefix}product_interest.user_id AND {$wpdb->prefix}product_interest.product_interest_id='".$product_interest_id."' AND {$wpdb->prefix}posts.ID={$wpdb->prefix}product_interest.product_id" );
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
            $subject = "Bestbuy-bestsell: A Business Aggregator\n\n";
            ob_start();
            include("email_header.php");
            ?>

            <p>Dear&nbsp;<?php echo $dear_text; ?> </p><br/>
            <p><?php echo $email_message_text; ?> </p><br/>
            <p>Your Interest Details:</p>
            <p><b>Product Name: </b>
            <a href="<?php echo get_site_url(); ?>/index.phpmy-interest-lists/?action=edit&product_interest_id=<?php echo $results_interest[0]->product_interest_id; ?>&product_name=<?php echo $results_interest[0]->post_name; ?>" > <?php echo $results_interest[0]->product_name; ?></a></p><br/>
            <p><b>Qty: </b><?php echo $results_interest[0]->interest_qty; ?> </p><br/>
            <p><b>Interest Start Date: </b><?php echo $interest_start_date; ?> </p><br/>
            <p><b>Interest End Date: </b><?php echo $interest_end_date; ?> </p><br/>
            <?php
            include("email_footer.php");
            $message = ob_get_contents();
            ob_end_clean();
            //$mailto = "tahersumonabu@gmail.com";
            //$subject = "This is test";
            //$message_body="This is message body";
            $from = "info@delvedivine.com";
            //wp_mail($mailto, $subject, $message_body);
            wp_mail($email_to,$subject,$message);
            remove_filter ("wp_mail_content_type", "bestbuy_bestsell_mail_content_type");
        }
}

    /** Author: ABU TAHER, Logic-coder IT
     * send_email_to_interest_group
     * Param $email_data, $group_details
     * return Success/Failure Message
     */
     function send_email_to_interest_group( $email_data, $group_details ){
        global $wpdb, $current_user;
        $bestbuy_bestsell_interest_list_object = new Bestbuybestsell_Interest();
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

            $group_price_list = $bestbuy_bestsell_interest_list_object ->get_group_price_by_id( $group_details[0]['group_id'] , $group_price_id='' );

            if( $group_price_list ){
                foreach( $group_price_list as $group_price_data ) {
                    $group_price_list_text .='<tr>
							<td><span>'. $group_price_data["no_of_sells"] .'</span></td>
							<td><span>'.$group_price_data["bestbuy_bestsell_price"] .'&nbsp;'.get_currency().'</span></td>
							<td><span>'.$group_price_data["shipping_price"] .'&nbsp;'.get_currency().'</span></td>
							</tr>'."\n\n";
                }
            }
            foreach( $group_details as $individual_data ){
                /******************************************/
                $user_info =  get_userdata( $individual_data['user_id'] );
                $user_meta_info = get_user_meta( $individual_data['user_id'], '' , '' );
                //return (print_r( $user_meta_info )); exit;
                if( $user_meta_info['first_name'][0] ){
                    $dear_text = $user_meta_info['first_name'][0];
                }else{
                    $dear_text = $user_info->display_name;
                }
                if( $individual_data['interest_start_date'] ){
                    $interest_start_date = date("Y-m-d", $individual_data['interest_start_date'] );
                    $interest_end_date = date("Y-m-d", $individual_data['interest_end_date'] );
                }else{ $interest_start_date = __("As soon as price is reasonable"); }
                /////////////////////// Start: Email Template ///////////////////////
                $subject="Bestbuybestsell: ".$email_data["email_subject"]." CaseNo(".$group_details[0]['group_id'] ."_".$individual_data['product_interest_id'] .")\n\n";
                ob_start();
                include("email_header.php");
                ?>
                <p>Dear Customer &nbsp;<?php echo $dear_text;?> </p><br/>
                <p><?php echo $email_data["email_message_to_interest_grp"];?></p><br/>
                <?php
                if( $email_data["same_price_to_all"] || !intval( $individual_data['interest_unit_price'] )){
                    if( $email_data["same_price_to_all"] ){
                        $same_price_to_all = 1;
                    }
                    ?>
                    <p>A Price List is following for your interest:</p><br/>
                    <table cellpadding='5' cellspacing='2' bgcolor=#ffffff width='100%' style='margin:0 auto'>
                        <tr style='font-family: Verdana,Arial,Helvetica,sans-serif; font-size: 11px; color: 333333; line-height: 140%;'>
                            <td><span style='font-weight:bold;'>No Of Sells</span></td>
                            <td><span style='font-weight:bold;'>Unit Price</span></td>
                            <td><span style='font-weight:bold;'>Shipping Price</span></td>
                        </tr><br/><br/>
                        <?php echo $group_price_list_text;?> </table><br/><br/>
                    <?php
                }else{ ?>
                    <p>The Unit Price For Your Interest Is:&nbsp;<?php echo get_currency().":".$individual_data['interest_unit_price'];?></p><br/>
                <?php } ?>
                <p>Your Interest Details:</p><br/>
                <p><b>Product Name: </b>
                    <a href="<?php echo get_site_url(); ?>/index.php/my-interest-lists/?action=edit&product_interest_id=<?php echo $individual_data['product_interest_id'];?> &product_name=<?php echo $individual_data['post_name'];?> " ><?php echo $individual_data['product_name'];?> </a></p><br/>
                <p><b>Qty: </b><?php echo $individual_data['interest_qty'];?></p><br/>
                <p><b>Interest Start Date: </b><?php echo $interest_start_date;?></p><br/>
                <p><b>Interest End Date: </b><?php echo $interest_end_date;?></p><br/>
                <?php
                if( $email_data['confirmation_within'] ){ ?>
                    <p>You Have&nbsp;<?php echo $email_data['confirmation_within'];?>Hours to confirm that you are still want to purchase this product for the above Details</p><br/>
                <?php } ?>
                <p>To confirm Please click on Yes:
                    <a href="<?php echo get_site_url();?>/index.php/my-interest-lists/?action=interest_confirmed&product_interest_id=<?php echo $individual_data['product_interest_id'];?>" >Yes</a>
                    <a href="<?php echo get_site_url();?>/index.php/my-interest-lists/?action=interest_notconfirmed&product_interest_id=<?php echo  $individual_data['product_interest_id'];?>" >No</a>
                </p><br/>
                <?php
                include("email_footer.php");
                $message = ob_get_contents();
                ob_end_clean();
                //echo $message; exit;
                /////////////////////// End: Email Template ///////////////////////
                $email_to = $user_info->user_email;
                $format_array = array('%s', '%d', '%d', '%s', '%s',  '%s',  '%d',  '%s');
                if( wp_mail($email_to, $subject, $message )){
                    $case_data['case_no'] = $group_details[0]['group_id'] ."_".$individual_data['product_interest_id'];
                    $case_data['product_interest_id'] = $individual_data['product_interest_id'];
                    $case_data['group_id'] = $group_details[0]['group_id'];
                    $case_data['user_id'] = $individual_data['user_id'];
                    $case_data['case_subject'] = $subject;
                    $case_data['case_message'] = $header;
                    $case_data['confirmation_within'] = $email_data['confirmation_within'];
                    $case_data['same_price_to_all'] = $same_price_to_all;
                    $case_data['add_date'] = $add_date;
                    //print_r( $case_data ); exit;
                    $succes_case_insert = $bestbuy_bestsell_interest_list_object ->insert_interest_case( $case_data , $format_array );
                    if( !$email_sent ){
                        $update_group_data = array( "email_sent"=>1, "same_price_to_all"=>$same_price_to_all );
                        $where = array( "group_id"=>$group_details[0]['group_id'] );
                        $update_format_array = array( '%d', "%d" );
                        $where_format = array();
                        $wpdb->update( "{$wpdb->prefix}interest_group" , $update_group_data, $where, $update_format_array = null, $where_format = null );
                        $email_sent = 1;
                    }
                    $update_interest_data = array( "interest_confirmation_link_expire"=>$interest_confirmation_link_expire_text );
                    $where = array( "product_interest_id"=>$individual_data['product_interest_id'] );
                    $update_format_array = array( '%s' );
                    $where_format = array();
                    $wpdb->update( "{$wpdb->prefix}product_interest" , $update_interest_data, $where, $update_format_array = null, $where_format = null );
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
     * Param $email_data, $interest_confirmed_details
     * return Success/Failure Message
     */
    function send_email_to_interest_confirmed( $email_data, $interest_confirmed_details, $deal_selection ){
        global $wpdb, $current_user;
        $bestbuy_bestsell_interest_list_object = new Bestbuybestsell_Interest();
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
            $count_interest_confirmed = $bestbuy_bestsell_interest_list_object ->count_interest_confirmed( $interest_confirmed_details[0]['product_id'], $interest_confirmed_details[0]['group_id'] );
            //echo $count_interest_confirmed[0]->total_qty; exit;
            //////////////////////////////////////////////////////
            $product_meta_values = get_post_meta( $interest_confirmed_details[0]['product_id'], '', '' );
            $minimum_target_sells = $product_meta_values['minimum_target_sells'][0];
            if( $count_interest_confirmed[0]['total_qty'] < $minimum_target_sells ){
                $group_price_list_matched = $bestbuy_bestsell_interest_list_object ->get_minimum_price_list( $interest_confirmed_details[0]['group_id'] );
            }else{
                $group_price_list_matched = $bestbuy_bestsell_interest_list_object ->get_group_price_list_matched( $interest_confirmed_details[0]['group_id'], $count_interest_confirmed[0]['total_qty'] );
            }
            /////////////////////////////////////////////////////
            if( $group_price_list_matched ){
                foreach( $group_price_list_matched as $group_price_data ) {
                    $group_price_list_text .='<tr>
						<td><span>'. $group_price_data["no_of_sells"] .'</span></td>
						<td><span>'.$group_price_data["bestbuy_bestsell_price"] .'&nbsp;'.get_currency().'</span></td>
						<td><span>'.$group_price_data["shipping_price"] .'&nbsp;'.get_currency().'</span></td>
						</tr>'."\n\n";
                }
            }
            foreach( $interest_confirmed_details as $individual_data ){
                /******************************************/
                $user_info =  get_userdata( $individual_data['user_id'] );
                $user_meta_info = get_user_meta( $individual_data['user_id'], '' , '' );
                //return (print_r( $user_meta_info )); exit;
                if( $user_meta_info['first_name'][0] ){
                    $dear_text = $user_meta_info['first_name'][0];
                }else{
                    $dear_text = $user_info->display_name;
                }
                if( $individual_data['interest_start_date'] ){
                    $interest_start_date = date("Y-m-d", $individual_data['interest_start_date'] );
                    $interest_end_date = date("Y-m-d", $individual_data['interest_end_date'] );
                }else{ $interest_start_date = __("As soon as price is reasonable"); }
                /////////////////////// Start: Email Template ///////////////////////
                $subject="Bestbuybestsell: ".$email_data["email_subject"]." CaseNo(".$interest_confirmed_details[0]['group_id'] ."_".$individual_data['product_interest_id'] .")\n\n";
                ob_start();
                include("email_header.php");
                ?>
                <p>Dear Customer &nbsp;<?php echo $dear_text;?></p>
                <p><?php echo $email_data["email_message_to_interest_grp"];?> </p>
                <?php
                if( $deal_selection ==='want_to_deal' ){
                    if( $individual_data['same_price_to_all'] || !intval( $individual_data['interest_unit_price'] ) ){
                    ?>
                        //$same_price_to_all = 1;
                        <p>A Price List is following for your interest:</p>
                        <table cellpadding='5' cellspacing='2' bgcolor=#ffffff width='100%' style='margin:0 auto'>
                        <tr style='font-family: Verdana,Arial,Helvetica,sans-serif; font-size: 11px; color: 333333; line-height: 140%;'>
						<td><span style='font-weight:bold;'>No Of Sells</span></td>
						<td><span style='font-weight:bold;'>Unit Price</span></td>
						<td><span style='font-weight:bold;'>Shipping Price</span></td>
						</tr>
                        <?php echo $group_price_list_text;?></table>
                    <?php
                    }else{ ?>
                        <p>The Unit Price For Your Interest Is:<?php echo get_currency()." : ".$individual_data['interest_unit_price'];?></p>
                    <?php }
                }?>
                <p>Your Interest Details:</p>
                <p><b>Product Name: </b>
                <a href="<?php echo get_site_url();?>/index.php/my-interest-list/?action=edit&product_interest_id=<?php echo $individual_data['product_interest_id'];?>&product_name=<?php echo $individual_data['post_name'];?>" ><?php echo $individual_data['product_name'];?> </a></p>
                <p><b>Qty: </b><?php echo $individual_data['interest_qty'];?> </p>
                <p><b>Interest Start Date: </b><?php echo $interest_start_date;?></p>
                <p><b>Interest End Date: </b><?php echo $interest_end_date;?></p>
                <?php
                if( $deal_selection=="want_to_deal"){
                    if( $email_data['payment_within'] ){ ?>
                        <p>You Have&nbsp;<?php echo $email_data['payment_within'];?>Hours For Payment to confirm that you are still want to purchase this product for the above Details</p>
                   <?Php } ?>
                    <p>For Payment Please click on This Link:
                        <a href="<?php echo get_site_url();?>/index.php/my-interest-list/?action=interest_confirmed&product_interest_id=<?php echo $individual_data['product_interest_id'];?>" >Yes</a>
					</p>
                <?php }
                include("email_footer.php");
                $message = ob_get_contents();
                ob_end_clean();
                $email_to = $user_info->user_email;
                $update_format_array = array( '%s', "%s", "%s" );
                //if( mail( $email_to , $subject,"",$header) )	{
                if( wp_mail( $email_to, $subject, $message) )	{
                    //$case_data['product_interest_id'] = $individual_data->product_interest_id;
                    $update_case_data['payment_subject'] = $subject;
                    $update_case_data['payment_message'] = $header;
                    $update_case_data['payment_within'] = $email_data['payment_within'];
                    $where = array( "product_interest_id"=>$individual_data['product_interest_id'] );
                    $where_format = array();
                    $wpdb->update( "{$wpdb->prefix}interest_group_case", $update_case_data, $where, $update_format_array = null, $where_format = null );
                    //print_r( $case_data ); exit;
                    //$succes_case_insert = insert_interest_case( $case_data , $format_array );
                    if( !$email_sent ){
                        $update_group_data = array( "payment_email_sent"=>1 );
                        $where = array( "group_id"=>$interest_confirmed_details[0]['group_id'] );
                        $update_format_array = array( '%d' );
                        $where_format = array();
                        $wpdb->update( "{$wpdb->prefix}interest_group", $update_group_data, $where, $update_format_array = null, $where_format = null );
                        $email_sent = 1;
                    }
                    if( $deal_selection=="want_to_deal" ){
                        $update_interest_data = array( "interest_campaign_closed"=>0 , "payment_confirmation_link_expire"=>$payment_confirmation_link_expire_text );
                        $update_format_array = array( '%d', '%s' );
                    }elseif( $deal_selection=="dealings_fail" ){
                        $update_interest_data = array( "interest_confirmed"=>0 , "interest_campaign_closed"=>2 ,"interest_confirmation_link_expire"=> 0 );
                        $update_format_array = array( '%d', '%d', '%s' );
                    }
                    $where = array( "product_interest_id"=>$individual_data['product_interest_id'] );
                    $where_format = array();
                    $wpdb->update( "{$wpdb->prefix}product_interest", $update_interest_data, $where, $update_format_array = null, $where_format = null );
                }
                /******************************************/
            }
        }
        if( $email_sent ){
            return True;
        }
    }
/**
* Bestbuy-bestsell Interest Groups Section
*
* @param $current_subtab
* @return Bestbuy-bestsell Interest Groups Section
*/
    add_action( 'bestbuy_bestsell_business_menu_section_settings_interest_groups', 'bestbuy_bestsell_interest_groups_section' ,10, 1 );
    function bestbuy_bestsell_interest_groups_section( $current_subtab ){
        global $build_subtab;
        $build_subtab = empty( $current_subtab ) ? 'interest_groups' : sanitize_title( $current_subtab );
        do_action('bestbuy_bestsell_business_menu_section_interest_groups_'.$build_subtab );
    }
    /**
     * Bestbuy-bestsell Interest Groups
     *
     * @param $current_subtab
     * @return Interest Groups
     */
    add_action('bestbuy_bestsell_business_menu_section_interest_groups_interest_groups', 'bestbuy_bestsell_interest_groups' );
    function bestbuy_bestsell_interest_groups( ){
        global $build_subtab , $user_action ;
        $bestbuy_bestsell_interest_list_object = new Bestbuybestsell_Interest();

        switch( $user_action ){
            case 'make-as-group':
                $search_text = __( 'Search Groups' );
                $bestbuy_bestsell_interest_list_object->make_interest_groups();
                break;

            case 'view-group-details':
            case 'add-more-interests':
                $search_text = __( 'Search Interests' );
                break;

            case 'set-group-price':
                $search_text = __( 'Search Group Price' );
                break;

            default:
                $search_text = __( 'Search Groups' ) ;
        }
        echo '<form method="post" id="bestbuy_bestsell_interest_groups">';
        $bestbuy_bestsell_interest_list_object->prepare_interest_lists_items( );
        $bestbuy_bestsell_interest_list_object->search_box( $search_text , 'product_interest_groups_search' );
        //$this->display();
        $bestbuy_bestsell_interest_list_object->display();
        //echo '<input type="hidden" name="action" value="'. $inmid_interest_list_object->current_action(). '" />';
        echo '</form>';
    }
    /* Bestbuy-bestsell Group Price Form Settings
    * Show group_price_form
    */
    add_action( 'bestbuy_bestsell_group_price_form_settings', 'group_price_form_settings' , 5 );
    function group_price_form_settings(){
        global $current_subtab, $current_tab, $product_id, $group_info, $search, $build_subtab, $subtabs,	$sql_posts_total, $product_interest_lists, $product_id, $user_action, $action, $group_price_form_settings_fields, $form_validation_errors, $price_data,$price_data_by_id ;

        $price_data_by_id = '';

        if( isset( $action ) && 'edit-group-price' === $action ){
            $bestbuy_bestsell_group_price_object = new Bestbuybestsell_Interest();
            $bestbuy_bestsell_group_price_object ->get_group_price_by_id( $group_id= '', $_REQUEST['group_price_id'] );
        }
        if( $group_info ){
            $submit_btn_name = 'price_save';
            $submit_btn_value = __( 'Save Price' , TEXTDOMAIN );

            if( $price_data_by_id ){
                $price_data['group_price_id'] = $price_data_by_id[0]['group_price_id'];
                $price_data['no_of_sells'] = $price_data_by_id[0]['no_of_sells'];
                $price_data['bestbuy_bestsell_price'] = $price_data_by_id[0]['bestbuy_bestsell_price'];
                $price_data['vendor_price'] = $price_data_by_id[0]['vendor_price'];
                $price_data['shipping_price'] = $price_data_by_id[0]['shipping_price'];
            }
        }

        $group_price_form_settings_fields = array(

            'no_of_sells' => array(
                'label'       => __( 'No Of Sells', TEXTDOMAIN ),
                'name'        => 'no_of_sells',
                'id'        => 'no_of_sells',
                'type'        => 'text',
                'value'     => $price_data['no_of_sells'] ? $price_data['no_of_sells'] : '',
                'tooltip_label' => __( 'Price Sets based on number of sells', TEXTDOMAIN ),
                'mandatory'       => 'yes',
            ),

            'bestbuy_bestsell_price' => array(
                'label'       => __( 'Bestbuy-bestsell Price', TEXTDOMAIN ),
                'name'        => 'bestbuy_bestsell_price',
                'id'        => 'bestbuy_bestsell_price',
                'type'        => 'text',
                'value'     => $price_data['bestbuy_bestsell_price'] ? $price_data['bestbuy_bestsell_price'] : '',
                'tooltip_label' => __( 'Bestbuy-bestsell Price for consumer', TEXTDOMAIN ),
                'mandatory'       => 'yes',
            ),

            'vendor_price' => array(
                'label'       => __( 'Vendor Price', TEXTDOMAIN ),
                'name'        => 'vendor_price',
                'id'        => 'vendor_price',
                'type'        => 'text',
                'value'     => $price_data['vendor_price'] ? $price_data['vendor_price'] : '',
                'tooltip_label' => __( 'Vendor Price for Bestbuy-bestsell', TEXTDOMAIN ),
                'mandatory'       => 'yes',
            ),

            'shipping_price' => array(
                'label'       => __( 'Shipping Price', TEXTDOMAIN ),
                'name'        => 'shipping_price',
                'id'        => 'shipping_price',
                'type'        => 'text',
                'value'     => $price_data['shipping_price'] ? $price_data['shipping_price'] : '',
                'tooltip_label' => __( 'Shipping Price for Bestbuy-bestsell consumer', TEXTDOMAIN ),
            ),

            'group_id' => array(
                'label'       => '',
                'name'        => 'group_id',
                'id'        => 'group_id',
                'type'        => 'hidden',
                'value'     => $_REQUEST['group_id'] ? $_REQUEST['group_id'] : '',
            ),

        );

        if(  'edit-group-price' === $action ){
            $submit_btn_name = "price_update";
            $submit_btn_value = __( 'Update Price' , TEXTDOMAIN );
            $group_price_form_settings_fields [ 'group_price_id' ] = array(
                'label'       => '',
                'name'        => 'group_price_id',
                'id'        => 'group_price_id',
                'type'        => 'hidden',
                'value'     => $_REQUEST['group_price_id'] ? $_REQUEST['group_price_id'] : '',	);
        }

        $group_price_form_settings_fields [ 'submit_button' ] = array(
            'label'       => $submit_btn_value,
            'name'        => $submit_btn_name,
            'id'        => 'product_description_header',
            'type'        => 'submit_button', );

        do_action( 'bestbuy_bestsell_show_product_interest_description_header_form',  $group_price_form_settings_fields );
    }
    /* Bestbuy-bestsell Product Interest Description Header Form Messages
    * Show product_interest_description_header_form_messages, Messages could be 'Error Messages', 'Sucess Messages', 'Failure Messages'
    * Return Bestbuy-bestsell Product Interest Description Header Form Messages
    */
    add_action( 'bestbuy_bestsell_show_product_interest_description_header_form' , 'product_interest_description_header_form_messages', 1 , 1 );
    function product_interest_description_header_form_messages( $form_fields ){
        global $form_validation_errors;
        ?>
        <div class='product_header_form_messages' >
            <?php
            echo '<p class="sucess_messages">';
            if( isset( $_SESSION['price_saved'] ) && $_SESSION['price_saved'] ){
                _e( 'Price Successfully Saved' );
                $price_data = array();
                $_SESSION['price_saved'] = '';
            }
            if( isset( $_SESSION['price_updated'] ) && $_SESSION['price_updated'] ){
                _e( 'Price Successfully Updated' );
                $price_data = array();
                $_SESSION['price_updated'] = '';
                $submit_btn_name = "price_save";
                $submit_btn_value = "Save Price";
            }
            if( isset( $_SESSION['group_email_sent'] ) && $_SESSION['group_email_sent'] ){
                _e( 'E-mail Successfully Sent' );
                $price_data = array();
                $_SESSION['group_email_sent'] = '';
            }

            echo '</p>';
            if ( isset( $form_validation_errors )  && sizeof( $form_validation_errors->get_error_messages() )  > 0 )  {
                echo '<p class="error_messages">';
                foreach ( $form_validation_errors->get_error_messages($code) as $error ) {
                    echo $error . "<br />";
                }
                echo '</p>';
            }
            ?>
        </div>
        <?php
    }
    /* Bestbuy-bestsell Product Interest Description Header Form
    * Show bestbuy_bestsell_show_product_interest_description_header_form
    * Return Bestbuy-bestsell Product Interest Description Header Form
    */
    add_action('bestbuy_bestsell_show_product_interest_description_header_form',  'show_product_interest_description_header_form' , 2 , 1 );
    function show_product_interest_description_header_form( $form_fields ){

        if( $form_fields ){
            echo '<form method="post" enctype="multipart/form-data" id="product_interest_description_header_form" name="product_interest_description_header_form" >';
            echo '<table class="form-table"><tbody>';
            foreach( $form_fields as $field ) {
                echo '<tr valign="top">';
                switch ( $field['type'] ) {
                    case 'label':
                        if( 'table_column_td' === $field['position']  ){
                            echo '<th class="titledesc" scope="row"></th>
									<td class="'. $field['class'].'"><label >'.$field['label'].'</label></td>';
                        }
                        else{
                            echo '<th class="titledesc '. $field['class'].'" scope="row"><label >'.$field['label'].'</label></th><td></td>';
                        }
                        break;
                    case 'radio':
                        //if( 'deal_selection' === $field['name'] ){
                        echo '<th class="titledesc" scope="row"></th>
								<td class="'. $field['class'].'"><input type="radio" name="'. $field['name']. '" id="'. $field['id']. '" value="'. $field['value']. '" />&nbsp;'.$field['label']."</td>";

                        //}
                        break;
                    case 'checkbox':
                        echo '<th class="titledesc" scope="row"><label><strong>'.$field['label'].'</strong></label>'
                            .get_mandatory_field_html( $field ) . get_tooltip_html( $field ).
                            '</th>
								<td class="forminp"><input '. $field['checked'].' type="checkbox" name="'. $field['name']. '" id="'. $field['id']. '"  />&nbsp;'.$field['description']."</td>";
                        break;
                    case 'text': // The html to display for the text type
                        echo '<th class="titledesc" scope="row"><label><strong>'.$field['label'].' </strong></label> '
                            .get_mandatory_field_html( $field ) . get_tooltip_html( $field ).
                            '</th>
								<td class="forminp">';
                        echo '<input '. $field['disabled']. ' type="text" name="'. $field['name']. '" id="'. $field['id']. '" value="'.$field['value']. '" ' .'" placeholder="'.$field['placeholder']. '" ' . $field['attribute'] .'/>'."</td>";
                        break;
                    case 'email': // The html to display for the text type
                        echo '<th class="titledesc" scope="row"><label><strong>'.$field['label'].'</strong></label>'
                            .get_mandatory_field_html( $field ) . get_tooltip_html( $field ).
                            '</th>
								<td class="forminp">';
                        echo '<input '. $field['disabled']. ' type="email" name="'. $field['name']. '" id="'. $field['id']. '" value="'.$field['value']. '" ' .'" placeholder="'.$field['placeholder']. '" ' . $field['attribute'] .' class="requiredField" />'."</td>";
                        break;
                    case 'select': // The html to display for the text type
                        echo '<th class="titledesc" scope="row"><label><strong>'.$field['label'].'</strong></label>'
                            .get_mandatory_field_html( $field ) .get_tooltip_html( $field ).
                            '</th>
								<td class="forminp">';
                        echo '<select '. $field['disabled']. ' name="'. $field['name']. '" id="'. $field['id']. '" >';
                        foreach( $field['options'] as $each_option ){
                            echo '<option '. $each_option['selected'].'  value="'.$each_option['value']. '" >' . $each_option['label']. '</option>';
                        }
                        echo '</select ></td>';
                        break;
                    case 'textarea': // The html to display for the textarea type
                        echo '<th class="titledesc" scope="row"><label><strong>'.$field['label'].'</strong></label>'
                            .get_mandatory_field_html( $field ) . get_tooltip_html( $field ).
                            '</th>
								<td class="forminp">';
                        echo '<textarea name="'. $field['name']. '" id="'.$field['id']. '"placeholder="'. $field['placeholder']. '">'. $field['value']. '</textarea></td>';
                        break;
                    case 'texteditor':
                        echo '<th class="titledesc" scope="row"><label><strong>'.$field['label'].'</strong></label>'
                            .get_mandatory_field_html( $field ) .get_tooltip_html( $field ).
                            '</th><td class="forminp">';
                        wp_editor( $field['value'] , 'product-textarea', $field['settings']);
                        echo '</td>';
                        break;
                    case 'hidden': // The html to display for the text type
                        echo '<th class="titledesc" scope="row"></th>
								<td class="forminp">';
                        echo '<input type="hidden" name="'. $field['name']. '" id="'. $field['id']. '" value="'.$field['value']. '"' . $field['attribute'] .'/>'."</td>";
                        break;
                }
                if( $field['type'] === 'submit_button' ){
                    echo '<th class="titledesc" scope="row"></th><td class="forminp">';
                    echo '<input style="display:'.$field['display'].'; " name="'.$field['name'].'" id="'.$field['id'].'" class="button-primary" type="submit" value="'.$field['label'].'" /></td>';
                }
            }
            echo '</tr></tbody></table>';
            wp_nonce_field( );
            echo '</form>';
        }
    }
    /* Bestbuy-bestsell  Group Price Save
    * bestbuy_bestsell_group_price_save
    * Return Success / Failure Message
    */
    add_action('bestbuy_bestsell_group_price_save', 'group_price_save', 1 , 1 );
    function group_price_save( $form_fields ){

        global $sanitize_post_data, $messages, $form_validation_errors, $price_data;

        $messages = array();
        $sanitize_post_data = array();
        $save_success = 0;
        $update_success= 0;
        //$form_validation_errors = new WP_Error();

        if(	isset( $_POST[ 'price_save' ] ) || isset( $_POST[ 'price_update' ] ) ){

            do_action( 'bestbuy_bestsell_group_price_form_validation' );
            if ( isset( $form_validation_errors ) && sizeof( $form_validation_errors->get_error_messages() ) > 0 )  		 {
                //print_r( $form_validation_errors );
            }else{
                //validate_settings_fields( $form_fields , 'product_interest_header_form' );
                $bestbuy_bestsell_group_price_object = new Bestbuybestsell_Interest();
                $price_data['group_id'] = $_REQUEST['group_id'];

                if(	isset( $_POST[ 'price_save' ] ) ){
                    $price_data['add_date'] = date("Y-m-d");
                    $save_success = $bestbuy_bestsell_group_price_object ->set_group_price( $price_data );
                }elseif(	isset( $_POST[ 'price_update' ] ) ){
                    $group_price_id = $_REQUEST['group_price_id'];
                    $update_success = $bestbuy_bestsell_group_price_object ->update_group_price( $price_data , $group_price_id );
                }
            }
        }
        if( $save_success ){
            $_SESSION['price_saved'] = 1;
            wp_safe_redirect(  add_query_arg( $url_param )  );
            exit;
        }
        if( $update_success ){
            $_SESSION['price_updated'] = 1;
            $url_param = array(
                'action' => false,
                'group_price_id=' => false,
            );
            wp_safe_redirect(  add_query_arg( $url_param )  );
            exit;
        }
        //print_r( $sanitize_post_data );
    }
    /* Bestbuy-bestsell  Group Price Form Validation
    * bestbuy_bestsell_group_price_form_validation
    * Return Validation Error Messages
    */
    add_action( 'bestbuy_bestsell_group_price_form_validation', 'group_price_form_validation' , 10 );
    function group_price_form_validation(  ){
        global $form_validation_errors, $price_data;
        $form_validation_errors = new WP_Error();

        $price_data['no_of_sells'] = isset( $_POST['no_of_sells'] ) ? absint( $_POST['no_of_sells'] ) : '' ;
        $price_data['bestbuy_bestsell_price'] = isset( $_POST['bestbuy_bestsell_price'] ) ? (double) $_POST['bestbuy_bestsell_price']  : '' ;
        $price_data['vendor_price'] = isset( $_POST['vendor_price'] ) ? (double) $_POST['vendor_price']  : '' ;
        $price_data['shipping_price'] = isset( $_POST['shipping_price'] ) ? (double) $_POST['shipping_price']  : '' ;

        if( empty( $price_data['no_of_sells'] ) ){
            $form_validation_errors->add('empty_no_of_sells', __("No Of Sells Can't be empty!!!'") );
        }
        if( empty( $price_data['bestbuy_bestsell_price'] ) ){
            $form_validation_errors->add('empty_bestbuy_bestsell_price', __("Bestbuy-bestsell Price Can't be empty!!!'") );
        }
        if( empty( $price_data['vendor_price'] ) ){
            $form_validation_errors->add('empty_vendor_price', __("Vendor Price Can't be empty!!!'") );
        }
    }
    /**
     * Show show_product_interest_description_header for Bestbuy-bestsell Admin Menu
     * Show Product Title, Product Thumb , No of Interest, No of Interester, Group Name, Group Closing Date, Group Price settings form
     */
    add_action( 'bestbuy_bestsell_show_product_interest_description_header' , 'show_product_interest_description_header' , 10 );
    function show_product_interest_description_header( ){
        global $current_subtab, $current_tab, $product_id, $group_info, $search, $build_subtab, $subtabs,$sql_posts_total, $product_interest_lists, $group_details,  $product_id, $user_action, $group_price_form_settings_fields, $sql_total_price_list, $count_interest_qty;
        $bestbuy_bestsell_interest_list_object = new Bestbuybestsell_Interest();
        $post_thumbnail = get_the_post_thumbnail( $product_id , 'medium' );
        $count_interest_qty = $bestbuy_bestsell_interest_list_object->sum_qty_for_product_interest( $product_id, "", $flag= "not_in_group" );
        if( $group_info ){
            /*if( $user_action === 'view-group-details'  ){
                $count_interest_qty = $inmid_interest_list_object->sum_qty_for_group_interest( $group_info[0]['product_id'] , $group_info[0]['group_id'] );

            }elseif( $user_action === 'view-failed-group-details'  ){
                $count_interest_qty = $inmid_interest_list_object->count_interest_failed( $group_info[0]['product_id'] , $group_info[0]['group_id'] );

            }*/

            $count_interest_qty = $bestbuy_bestsell_interest_list_object->sum_qty_for_group_interest( $group_info[0]['product_id'] , $group_info[0]['group_id'] );

            if( 'view-failed-group-details' === $user_action ){
                $count_interest_qty = $bestbuy_bestsell_interest_list_object->count_interest_failed( $group_info[0]['product_id'] , $group_info[0]['group_id'] );
            }
            if( 'view-confirmed-group-details' === $user_action || 'add-more-interests-to-confirmed-group' === $user_action ){
                $count_interest_qty = $bestbuy_bestsell_interest_list_object->count_interest_confirmed( $group_info[0]['product_id'] , $group_info[0]['group_id'] );
            }
            $bestbuy_bestsell_interest_list_object->get_group_price_by_id( $group_info[0]['group_id'], $group_price_id='' );
        }
        ?>
        <div class="product_interest_description_header">
            <h2>
                <?php
                echo get_the_title( $product_id ). " ( ";
                echo $count_interest_qty[0]['total_qty'] ? $count_interest_qty[0]['total_qty'] : '0';
                echo " Pc";
                echo $count_interest_qty[0]['total_qty'] >  1 ? 's' : '';
                echo ' ) <br /><br />';
                _e("Total Interester: ");
                echo $sql_posts_total;
                ?>
            </h2>
            <!-- Start: interest_details -->
            <div class="interest_details">
                <div class="product_thumb"> <?php echo $post_thumbnail;  ?>	</div>

                <!-- Start: group_info -->
                <div class="interest_info"> <br/>
					<span class="product_title">
						<?php
                        if( $group_info ){
                            _e('Group: ');
                            echo $group_info[0]['group_name'].'<br/><br/>';
                            _e('Group Closing: ');
                            if( $group_info[0]['group_closing_date']==='asap' ){ _e('asap'); }
                            else  {	echo date('Y-m-d', $group_info[0]['group_closing_date'] );	}

                            if( isset( $user_action ) && ( 'view-group-details' === $user_action || 'view-confirmed-group-details' === $user_action ) ){
                                echo '<br/><br/>';
                                _e('Group Price: ');

                                $url_param = array(
                                    'tab' => 'interest_groups',
                                    'user_action' => 'set-group-price',
                                );

                                echo '<a style="float:inherit;" href="' . add_query_arg( $url_param ) .'" >';

                                if( $sql_total_price_list ){ _e( 'Price Already Set' , TEXTDOMAIN ); } else  { _e( 'Price Not Set Yet' , TEXTDOMAIN ) ;	}
                                echo '</a><br/><br/>';

                                _e('E-mail To Group: ');

                                switch( $user_action ){
                                    case 'view-group-details':
                                        if( $group_info[0]['email_sent'] ){
                                            _e('E-mail Already Sent');
                                        }else{
                                            _e('E-mail Not Sent Yet');
                                        }
                                        break;

                                    case 'view-confirmed-group-details':
                                        if( $group_info[0]['payment_email_sent'] ){
                                            _e('E-mail Already Sent');
                                        }else{
                                            _e('E-mail Not Sent Yet');
                                        }
                                        break;
                                }
                            }
                        }
                        ?>
					</span>
                </div> 	<!-- End: group_info -->

            </div>	<!-- End: interest_details -->
        </div>	<br class="clear">
        <div class="product_header_form">
            <?php
            if( isset( $user_action ) && 'view-group-details' === $user_action ){
                do_action( 'bestbuy_bestsell_send_email_to_group' );
                do_action( 'bestbuy_bestsell_send_email_to_group_form_settings' );
            }elseif( isset( $user_action ) && 'view-confirmed-group-details' === $user_action ){
                do_action( 'bestbuy_bestsell_send_email_to_confirmed_group' );
                do_action( 'bestbuy_bestsell_send_email_to_group_form_settings' );
            }
            elseif( isset( $user_action ) && 'set-group-price' === $user_action ){
                do_action( 'bestbuy_bestsell_group_price_save', $group_price_form_settings_fields );
                do_action( 'bestbuy_bestsell_group_price_form_settings' );
            }
            ?>
        </div><br class="clear">
        <?php
    }
    /**
     *Show menu lists title for Bestbuy_bestsell Admin Menu
     *
     */
    add_action( 'bestbuy_bestsell_show_menu_lists_title', 'show_menu_lists_title', 10 );
    function show_menu_lists_title( ){
        global $current_subtab, $current_tab, $user_action ;

        switch( $current_tab ){
            case 'interest_lists':
                if( empty( $current_subtab ) && empty( $user_action ) ){
                    $title_message = __('Interest Lists');
                }elseif( isset( $user_action ) && 'product-interest-lists' === $user_action ){
                    $title_message = __('Product Interest Lists');
                }
                break;
            case 'interest_groups':
                if( empty( $current_subtab ) && empty( $user_action ) ){
                    $title_message = __('Interest Group Lists');
                }elseif( isset( $user_action ) && 'view-group-details' === $user_action ){
                    $title_message = __('Group Interest Lists');
                    $url_param = array(
                        'user_action' => 'add-more-interests',
                    );
                    $sub_sub_sub_menu = '<li class="all"> <a href="' . add_query_arg( $url_param ) . '">'. __("Add More Interests" , TEXTDOMAIN ). ' </a></li>';

                }elseif( isset( $user_action ) && 'set-group-price' === $user_action ){
                    $title_message = __('Group Price Lists');
                }elseif( isset( $user_action ) && 'add-more-interests' === $user_action ){
                    $title_message = __('Product Interest Lists');
                }
                break;
            case 'interest_failed_groups':
                if( empty( $current_subtab ) && empty( $user_action ) ){
                    $title_message = __('Failed Interest Group Lists');
                }elseif( isset( $user_action ) && 'view-failed-group-details' === $user_action ){
                    $title_message = __('Failed Interest Lists');
                }
                break;
            case 'interest_confirmed_groups':
                if( empty( $current_subtab ) && empty( $user_action ) ){
                    $title_message = __('Confirmed Interest Group Lists ');
                }elseif( isset( $user_action ) && 'view-confirmed-group-details' === $user_action ){
                    $title_message = __('Confirmed Interest Lists');
                    $url_param = array(
                        'user_action' => 'add-more-interests-to-confirmed-group',
                    );
                    $sub_sub_sub_menu = '<li class="all"> <a href="' . add_query_arg( $url_param ) . '">'. __("Add More Interests" , TEXTDOMAIN ). ' </a></li>';
                }elseif( isset( $user_action ) && 'add-more-interests-to-confirmed-group' === $user_action ){
                    $title_message = __('Product Interest Lists');
                }
                break;
        }
        echo '<h2 >'. $title_message . '</h2>';
        if( !empty( $sub_sub_sub_menu ) ){
            echo '<ul class="subsubsub">'. $sub_sub_sub_menu . '</ul>';
        }
    }
    /////////////////////////////////////////////////////////////////////
    /* Bestbuy-bestsell Send E-mail To Group Form Settings
    * Show Send E-mail To Group Form
    */
    add_action( 'bestbuy_bestsell_send_email_to_group_form_settings', 'send_email_to_group_form_settings' , 5 );
    function send_email_to_group_form_settings(){
        global $current_subtab, $current_tab, $product_id, $group_info, $search, $build_subtab, $subtabs,	$sql_posts_total, $user_action, $action, $send_email_to_group_form_settings_fields, $email_data,  $form_validation_errors, $count_interest_qty ;

        $minimum_target_sells = get_post_meta( $group_info[0]['product_id'], 'minimum_target_sells', '' );

        $send_email_to_group_form_settings_fields = array(

            'email_subject' => array(
                'label'       => __( 'Subject', TEXTDOMAIN ),
                'name'        => 'email_subject',
                'id'        => 'email_subject',
                'type'        => 'text',
                'value'     => $email_data['email_subject'] ? $email_data['email_subject'] : '',
                'tooltip_label' => __( 'E-mail Subject', TEXTDOMAIN ),
                'mandatory'       => 'yes',
            ),

            'email_message_to_interest_grp' => array(
                'label'       => __( 'Message', TEXTDOMAIN ),
                'name'        => 'email_message_to_interest_grp',
                'id'        => 'email_message_to_interest_grp',
                'type'        => 'textarea',
                'value'     => $email_data['email_message_to_interest_grp'] ? $email_data['email_message_to_interest_grp'] : '',
                'tooltip_label' => __( 'E-mail Body', TEXTDOMAIN ),
                'mandatory'       => 'yes',
            ),

        );

        if( 'view-group-details' === $user_action ){
            $send_email_to_group_form_settings_fields['confirmation_within'] = array(
                'label'       => __( 'Confirmation Within', TEXTDOMAIN ),
                'name'       => 'confirmation_within',
                'id'       => 'confirmation_within',
                'type'        => 'select',
                'tooltip_label' => __( 'Interester Must Confirm their interest within this time period', TEXTDOMAIN ),
                'options'     => array(
                    array( 'label' => __( '--Please Select--', TEXTDOMAIN ),
                        'value' => '' ),

                    array( 'label' => __( '24Hours', TEXTDOMAIN ),
                        'value' => '24' ),

                    array( 'label' => __( '48Hours', TEXTDOMAIN ),
                        'value' => '48' ),

                    array( 'label' => __( '72Hours', TEXTDOMAIN ),
                        'value' => '72' )
                )
            );
        }
        if( 'view-confirmed-group-details' === $user_action ){
            $send_email_to_group_form_settings_fields['payment_within'] = array(
                'label'       => __( 'Payment Within', TEXTDOMAIN ),
                'name'       => 'payment_within',
                'id'       => 'payment_within',
                'type'        => 'select',
                'tooltip_label' => __( 'Interester Must Pay for their interest within this time period', TEXTDOMAIN ),
                'options'     => array(
                    array( 'label' => __( '--Please Select--', TEXTDOMAIN ),
                        'value' => '' ),

                    array( 'label' => __( '24Hours', TEXTDOMAIN ),
                        'value' => '24' ),

                    array( 'label' => __( '48Hours', TEXTDOMAIN ),
                        'value' => '48' ),

                    array( 'label' => __( '72Hours', TEXTDOMAIN ),
                        'value' => '72' )
                )
            );

            if( $count_interest_qty[0]['total_qty'] < $minimum_target_sells[0] ){
                $bestbuy_bestsell_interest_list_object = new Bestbuybestsell_Interest();
                $minimum_price_list = $bestbuy_bestsell_interest_list_object ->get_minimum_price_list( $group_info[0]['group_id'] );
                $send_email_to_group_form_settings_fields['mmq_not_reached'] =  array(
                    'label'   => __('MMQ', TEXTDOMAIN ). ' ( '. $minimum_target_sells[0].' )'.__( ' Not Reached.' , TEXTDOMAIN ),
                    'name'   => 'mmq_not_reached',
                    'id'   => 'mmq_not_reached',
                    'type'    => 'label',
                    'class' => 'mmq_not_reached',
                    'position' => 'table_column_td',
                );
                $deal_selection_label_text = $minimum_price_list ? $minimum_price_list[0]['inmid_price'].' '.get_currency().'/Unit?': '';
                $send_email_to_group_form_settings_fields['deal_selection'] =  array(
                    'label'   => __( 'Still Deal With Price: ', TEXTDOMAIN ). $deal_selection_label_text,
                    'name'   => 'deal_selection',
                    'id'   => 'deal_selection',
                    'type'    => 'radio',
                    'value' => 'want_to_deal',
                    'class' => 'deal_selection',
                    'position' => 'table_column_td',
                );
                $send_email_to_group_form_settings_fields['deal_selection_or'] =  array(
                    'label'   => __( 'Or', TEXTDOMAIN ),
                    'name'   => 'deal_selection_or',
                    'id'   => 'deal_selection_or',
                    'type'    => 'label',
                    'class' => 'deal_selection_or',
                    'position' => 'table_column_td',

                );
                $send_email_to_group_form_settings_fields['dealings_fail'] =  array(
                    'label'   => __( 'Campaign Fail Message', TEXTDOMAIN ),
                    'name'   => 'deal_selection',
                    'id'   => 'deal_selection',
                    'type'    => 'radio',
                    'value' => 'dealings_fail',
                    'class' => 'deal_selection',
                    'position' => 'table_column_td',
                );
            }
        }
        if( 'view-group-details' === $user_action ){
            $send_email_to_group_form_settings_fields['same_price_to_all'] =  array(
                'label'   => __( 'Send Same price to all', TEXTDOMAIN ),
                'name'   => 'same_price_to_all',
                'id'   => 'same_price_to_all',
                'type'    => 'checkbox',
                'tooltip_label'   => __( 'Send Group Price To All Interester When this option is enabled; Otherwise send individual price to respective Interester', TEXTDOMAIN ),
            );
        }
        $send_email_to_group_form_settings_fields['group_id'] = array(
            'label'       => '',
            'name'        => 'group_id',
            'id'        => 'group_id',
            'type'        => 'hidden',
            'value'     => $group_info[0]['group_id'] ? $group_info[0]['group_id'] : '',
        );
        $send_email_to_group_form_settings_fields['submit_button'] =  array(
            'label'       => __( 'Send E-mail', TEXTDOMAIN ),
            'name'        => 'send_email',
            'id'        => 'send_email',
            'type'        => 'submit_button',
            'display'        => $count_interest_qty[0]['total_qty'] < $minimum_target_sells[0] ? 'none':'',
        );
        if( 'view-group-details' === $user_action ){
            $send_email_to_group_form_settings_fields['submit_button'] =  array(
                'label'       => __( 'Send E-mail', TEXTDOMAIN ),
                'name'        => 'send_email',
                'id'        => 'send_email',
                'type'        => 'submit_button',
            );
        }
        do_action( 'bestbuy_bestsell_show_product_interest_description_header_form',  $send_email_to_group_form_settings_fields );
    }
    /* Bestbuy-bestsell  Send E-mail To Group
    * send_email_to_group
    * Return Success / Failure Message
    */
    add_action('bestbuy_bestsell_send_email_to_group', 'send_email_to_group', 1 , 1 );
    function send_email_to_group( $form_fields ){
        global $sanitize_post_data, $messages, $form_validation_errors, $email_data, $group_details ;
        $messages = array();
        $sanitize_post_data = array();
        $email_success = 0;

        if(	isset( $_POST[ 'send_email' ] ) ){

            do_action( 'bestbuy_bestsell_send_email_to_group_form_validation' );
            if ( isset( $form_validation_errors ) && sizeof( $form_validation_errors->get_error_messages() ) > 0 )  		 {
                //print_r( $form_validation_errors );
            }else{
                $bestbuy_bestsell_email_send_to_group = new Bestbuybestsell_Interest();
                //$email_success = $bestbuy_bestsell_email_send_to_group ->send_email_to_interest_group( $email_data, $group_details );
                $email_success = send_email_to_interest_group( $email_data, $group_details );
            }
        }
        if( $email_success ){
            $_SESSION['group_email_sent'] = 1;
            wp_safe_redirect(  add_query_arg( $url_param )  );
            exit;
        }
    }
    /* Bestbuy-bestsell  Send E-mail To Confirmed Group
    * send_email_to_confirmed_group
    *Return Success / Failure Message
    */
    add_action('bestbuy_bestsell_send_email_to_confirmed_group', 'send_email_to_confirmed_group', 1 , 1 );
    function send_email_to_confirmed_group( $form_fields ){

        global $sanitize_post_data, $messages, $form_validation_errors, $email_data, $group_details ;
        $messages = array();
        $sanitize_post_data = array();
        $email_success = 0;

        if(	isset( $_POST[ 'send_email' ] ) ){
            do_action( 'bestbuy_bestsell_send_email_to_group_form_validation' );
            if ( isset( $form_validation_errors ) && sizeof( $form_validation_errors->get_error_messages() ) > 0 )  		 {
                //print_r( $form_validation_errors );
            }else{
                $bestbuy_bestsell_email_send_to_group = new Bestbuybestsell_Interest();
                //$email_success = $bestbuy_bestsell_email_send_to_group ->send_email_to_interest_confirmed( $email_data, $group_details , $_REQUEST['deal_selection'] );
                $email_success = send_email_to_interest_confirmed( $email_data, $group_details , $_REQUEST['deal_selection'] );
            }
        }
        if( $email_success ){
            $_SESSION['group_email_sent'] = 1;
            wp_safe_redirect(  add_query_arg( $url_param )  );
            exit;
        }
    }
    /* Bestbuy-bestsell  Send E-mail To Group  Form Validation
    * bestbuy_bestsell_send_email_to_group_form_validation
    * Return Validation Error Messages
    */
    add_action( 'bestbuy_bestsell_send_email_to_group_form_validation', 'send_email_to_group_form_validation' , 10 );
    function send_email_to_group_form_validation(  ){
        global $form_validation_errors, $email_data;
        $form_validation_errors = new WP_Error();

        $email_data['group_id'] = isset( $_POST['group_id'] ) ? absint( $_POST['group_id'] ) : '' ;
        $email_data['email_subject'] = isset( $_POST['email_subject'] ) ?  $_POST['email_subject']  : '' ;
        $email_data['email_message_to_interest_grp'] = isset( $_POST['email_subject'] ) ?  $_POST['email_message_to_interest_grp']  : '' ;
        $email_data['confirmation_within'] = isset( $_POST['confirmation_within'] ) ?  absint( $_POST['confirmation_within'] )  : '' ;
        $email_data['payment_within'] = isset( $_POST['payment_within'] ) ?  absint( $_POST['payment_within'] )  : '' ;
        $email_data['same_price_to_all'] = isset( $_POST['same_price_to_all'] ) ?   $_POST['same_price_to_all']   : '' ;

        if( empty( $email_data['email_subject'] ) ){
            $form_validation_errors->add('empty_email_subject', __("Subject Can't be empty!!!") );
        }
        if( empty( $email_data['email_message_to_interest_grp'] ) ){
            $form_validation_errors->add('empty_email_message_to_interest_grp', __("Message Can't be empty!!!") );
        }
    }

    function get_mandatory_field_html( $data ) {
        return $data['mandatory'] ? '<span class="mandatory_field_class" >*</span>' : '';
    }
    /**
     * Bestbuy-bestsell Interest Failed Groups Section
     *
     * @param $current_subtab
     * @return Bestbuy-bestsell Interest Failed Groups Section
     */
    add_action( 'bestbuy_bestsell_business_menu_section_settings_interest_failed_groups', 'bestbuy_bestsell_interest_failed_groups_section' ,10, 1 );
    function bestbuy_bestsell_interest_failed_groups_section( $current_subtab ){
        global $build_subtab;
        $build_subtab = empty( $current_subtab ) ? 'interest_failed_groups' : sanitize_title( $current_subtab );
        do_action('bestbuy_bestsell_business_menu_section_interest_failed_groups_'.$build_subtab );
    }
    /**
     * Bestbuy-bestsell Interest Failed Groups
     *
     * @param $current_subtab
     * @return Interest Failed Groups
     */
    add_action('bestbuy_bestsell_business_menu_section_interest_failed_groups_interest_failed_groups', 'bestbuy_bestsell_interest_failed_groups' );
    function bestbuy_bestsell_interest_failed_groups( ){

        global $build_subtab , $user_action ;
        $bestbuy_bestsell_interest_list_object = new Bestbuybestsell_Interest();

        switch( $user_action ){
            case 'make-as-group':
                $search_text = __( 'Search Groups' );
                $bestbuy_bestsell_interest_list_object->make_interest_groups();
                break;

            case 'view-group-details':
            case 'view-failed-group-details':
            case 'add-more-interests':
                $search_text = __( 'Search Interests' );
                break;

            case 'set-group-price':
                $search_text = __( 'Search Group Price' );
                break;

            default:
                $search_text = __( 'Search Groups' ) ;
        }
        echo '<form method="post" id="bestbuy_bestsell_interest_groups">';
        $bestbuy_bestsell_interest_list_object->prepare_interest_lists_items( );
        $bestbuy_bestsell_interest_list_object->search_box( $search_text , 'product_interest_groups_search' );
        //$this->display();
        $bestbuy_bestsell_interest_list_object->display();
        //echo '<input type="hidden" name="action" value="'. $inmid_interest_list_object->current_action(). '" />';
        echo '</form>';
    }
    /**
     * Bestbuy-bestsell Interest Confirmed Groups Section
     *
     * @param $current_subtab
     * @return Bestbuy-bestsell Interest Confirmed Groups Section
     */
    add_action( 'bestbuy_bestsell_business_menu_section_settings_interest_confirmed_groups', 'bestbuy_bestsell_interest_confirmed_groups_section' ,10, 1 );
    function bestbuy_bestsell_interest_confirmed_groups_section( $current_subtab ){
        global $build_subtab;
        $build_subtab = empty( $current_subtab ) ? 'interest_confirmed_groups' : sanitize_title( $current_subtab );
        do_action('bestbuy_bestsell_business_menu_section_interest_confirmed_groups_'.$build_subtab );
    }
    /**
     * Bestbuy-bestsell Interest Confirmed Groups
     *
     * @param $current_subtab
     * @return Interest Confirmed Groups
     */
    add_action('bestbuy_bestsell_business_menu_section_interest_confirmed_groups_interest_confirmed_groups', 'bestbuy_bestsell_interest_confirmed_groups' );
    function bestbuy_bestsell_interest_confirmed_groups( ){
        global $build_subtab , $user_action ;
        $bestbuy_bestsell_interest_list_object = new Bestbuybestsell_Interest();

        switch( $user_action ){

            case 'view-confirmed-group-details':
            case 'add-more-interests-to-confirmed-group':
                $search_text = __( 'Search Interests' );
                break;

            default:
                $search_text = __( 'Search Groups' ) ;
        }
        echo '<form method="post" id="bestbuy_bestsell_interest_groups">';
        $bestbuy_bestsell_interest_list_object->prepare_interest_lists_items( );
        $bestbuy_bestsell_interest_list_object->search_box( $search_text , 'product_interest_groups_search' );
        //$this->display();
        $bestbuy_bestsell_interest_list_object->display();
        //echo '<input type="hidden" name="action" value="'. $inmid_interest_list_object->current_action(). '" />';
        echo '</form>';
    }

    /**
     * Bestbuy_bestsell Custom Product Post Type
     *
     * @return Bestbuy_bestsell Custom Product Post Type
     */
    add_action( 'init', 'bestbuy_bestsell_product_post_type' );
    function bestbuy_bestsell_product_post_type() {
        /***************** Bestbuy_bestsell Product ****************/
        $labels = array(
            'name'               => __( 'Bestbuy Bestsell Products', TEXTDOMAIN ),
            'singular_name'      => __( 'Bestbuy Bestsell Product', TEXTDOMAIN ),
            'menu_name'          => _x( 'Bestbuy Bestsell Products', 'Admin menu name', TEXTDOMAIN ),
            'add_new'            => __( 'Add Product', TEXTDOMAIN ),
            'add_new_item'       => __( 'Add New Product', TEXTDOMAIN ),
            'edit'               => __( 'Edit', TEXTDOMAIN ),
            'edit_item'          => __( 'Edit Product', TEXTDOMAIN ),
            'new_item'           => __( 'New Product', TEXTDOMAIN ),
            'view'               => __( 'View Product', TEXTDOMAIN ),
            'view_item'          => __( 'View Product', TEXTDOMAIN ),
            'search_items'       => __( 'Search Products', TEXTDOMAIN ),
            'not_found'          => __( 'No Products found', TEXTDOMAIN ),
            'not_found_in_trash' => __( 'No Products found in trash', TEXTDOMAIN ),
            'parent'             => __( 'Parent Product', TEXTDOMAIN )
        );

        $args = array(
            'labels'             => $labels,
            'description'         => __( 'This is where you can add new products to your store.', TEXTDOMAIN ),
            'public'             => true,
            'map_meta_cap'        => true,
            'publicly_queryable' => true,
            'exclude_from_search' => false,
            'show_ui'            => true,
            'can_export'         => true,
            'show_in_nav_menus'  => true,
            'query_var'          => true,
            'has_archive'        => true,
            'rewrite'            => array( 'slug' => 'product' ) ,
            'capability_type'    => 'post',
            'map_meta_cap'        => true,
            'show_in_nav_menus'   => true,
            'hierarchical'       => false,
            'menu_position'      => null,
            'supports'            => array( 'title', 'editor', 'excerpt', 'thumbnail', 'comments', 'custom-fields', 'page-attributes' ),
        );
        register_post_type( 'bestbuy_bestsell_product', $args );
        /***************** Bestbuy Bestsell Product Category ****************/
        $labels = array(
            'name'              => __( 'Product Categories', TEXTDOMAIN ),
            'singular_name'     => __( 'Product Category', TEXTDOMAIN ),
            'menu_name'         => _x( 'Categories', 'Admin menu name', TEXTDOMAIN ),
            'search_items'      => __( 'Search Product Categories', TEXTDOMAIN ),
            'all_items'         => __( 'All Product Categories', TEXTDOMAIN ),
            'parent_item'       => __( 'Parent Product Category', TEXTDOMAIN ),
            'parent_item_colon' => __( 'Parent Product Category:', TEXTDOMAIN ),
            'edit_item'         => __( 'Edit Product Category', TEXTDOMAIN ),
            'update_item'       => __( 'Update Product Category', TEXTDOMAIN ),
            'add_new_item'      => __( 'Add New Product Category', TEXTDOMAIN ),
            'new_item_name'     => __( 'New Product Category Name', TEXTDOMAIN )
        );
        register_taxonomy( 'bestbuy_bestsell_product_category', array( 'bestbuy_bestsell_product' ), array(
            'labels'            => $labels,
            'show_ui'           => true,
            'show_admin_column' => false,
            'query_var'         => true,
            'rewrite'        => array( 'slug' => _x( 'bestbuy-bestsell-product-category', 'slug', TEXTDOMAIN ) ) ,
            'with_front'   => false,
            'hierarchical' => true,
        ) );
        /***************** Product Tag ****************/
        $labels = array(
            'name'              => __( 'Product Tags', TEXTDOMAIN ),
            'singular_name'     => __( 'Product Tag', TEXTDOMAIN ),
            'menu_name'         => _x( 'Tags', 'Admin menu name', TEXTDOMAIN ),
            'search_items'      => __( 'Search Product Tags', TEXTDOMAIN ),
            'all_items'         => __( 'All Product Tags', TEXTDOMAIN ),
            'parent_item'       => __( 'Parent Product Tag', TEXTDOMAIN ),
            'parent_item_colon' => __( 'Parent Product Tag:', TEXTDOMAIN ),
            'edit_item'         => __( 'Edit Product Tag', TEXTDOMAIN ),
            'update_item'       => __( 'Update Product Tag', TEXTDOMAIN ),
            'add_new_item'      => __( 'Add New Product Tag', TEXTDOMAIN ),
            'new_item_name'     => __( 'New Product Tag Name', TEXTDOMAIN )
        );
        register_taxonomy( 'bestbuy_bestsell_product_tag', array( 'bestbuy_bestsell_product' ), array(
            'labels'            => $labels,
            'show_ui'           => true,
            'show_admin_column' => false,
            'query_var'         => true,
            'rewrite'        => array( 'slug' => _x( 'bestbuy-bestsell-product-tag', 'slug', TEXTDOMAIN ) ) ,
            'with_front'   => false,
            'hierarchical' => true,
        ) );
    }
    add_filter( 'manage_bestbuy_bestsell_product_posts_columns', 'bestbuy_bestsell_product_columns' );
    add_action( 'manage_bestbuy_bestsell_product_posts_custom_column',  'render_bestbuy_bestsell_product_columns' , 2 );
    add_filter( 'manage_edit-bestbuy_bestsell_product_sortable_columns', 'bestbuy_bestsell_product_sortable_columns' );
    add_action( 'restrict_manage_posts', 'restrict_manage_posts' );
    add_action( 'add_meta_boxes', 'bestbuy_bestsell_product_images_meta_box' );

    function bestbuy_bestsell_product_images_meta_box(){
        add_meta_box( 'woocommerce-product-images', __( 'Product Gallery', TEXTDOMAIN ), 'bestbuy_bestsell_meta_box_product_images', 'inmid_product', 'side' );
    }
    /**
     * Output the metabox
     */
    function bestbuy_bestsell_meta_box_product_images( $post ) {
        ?>
        <div id="product_images_container">
            <ul class="product_images">
                <?php
                if ( metadata_exists( 'post', $post->ID, '_product_image_gallery' ) ) {
                    $product_image_gallery = get_post_meta( $post->ID, '_product_image_gallery', true );
                } else {
                    // Backwards compat
                    $attachment_ids = get_posts( 'post_parent=' . $post->ID . '&numberposts=-1&post_type=attachment&orderby=menu_order&order=ASC&post_mime_type=image&fields=ids&meta_key=_woocommerce_exclude_image&meta_value=0' );
                    $attachment_ids = array_diff( $attachment_ids, array( get_post_thumbnail_id() ) );
                    $product_image_gallery = implode( ',', $attachment_ids );
                }

                $attachments = array_filter( explode( ',', $product_image_gallery ) );

                if ( $attachments ) {
                    foreach ( $attachments as $attachment_id ) {
                        echo '<li class="image" data-attachment_id="' . esc_attr( $attachment_id ) . '">
								' . wp_get_attachment_image( $attachment_id, 'thumbnail' ) . '
								<ul class="actions">
									<li><a href="#" class="delete tips" data-tip="' . __( 'Delete image', 'woocommerce' ) . '">' . __( 'Delete', 'woocommerce' ) . '</a></li>
								</ul>
							</li>';
                    }
                }
                ?>
            </ul>

            <input type="hidden" id="product_image_gallery" name="product_image_gallery" value="<?php echo esc_attr( $product_image_gallery ); ?>" />
        </div>
        <p class="add_product_images hide-if-no-js">
            <a href="#" data-choose="<?php _e( 'Add Images to Product Gallery', 'woocommerce' ); ?>" data-update="<?php _e( 'Add to gallery', 'woocommerce' ); ?>" data-delete="<?php _e( 'Delete image', 'woocommerce' ); ?>" data-text="<?php _e( 'Delete', 'woocommerce' ); ?>"><?php _e( 'Add product gallery images', 'woocommerce' ); ?></a>
        </p>
        <?php
    }

    /**
     * Filters for post types
     */
    function restrict_manage_posts() {
        global $typenow, $wp_query;

        if ( 'bestbuy_bestsell_product' == $typenow ) {
            bestbuy_bestsell_product_filters();
        }
    }

    /**
     * Show a category filter box
     */
    function bestbuy_bestsell_product_filters() {
        global $wp_query;
        // Category Filtering
        bestbuy_bestsell_product_dropdown_categories();
    }
    /**
     * Bestbuy_bestsell Dropdown categories
     *
     * We use a custom walker, just like WordPress does
     *
     * @param int $deprecated_show_uncategorized (default: 1)
     * @return string
     */
    function bestbuy_bestsell_product_dropdown_categories( $args = array(), $deprecated_hierarchical = 1, $deprecated_show_uncategorized = 1, $deprecated_orderby = '' ) {
        global $wp_query;

        if ( ! is_array( $args ) ) {
            _deprecated_argument( 'bestbuy_bestsell_product_dropdown_categories()', '3.0', 'show_counts, hierarchical, show_uncategorized and orderby arguments are invalid - pass a single array of values instead.' );

            $args['show_counts']      = $args;
            $args['hierarchical']       = $deprecated_hierarchical;
            $args['show_uncategorized'] = $deprecated_show_uncategorized;
            $args['orderby']            = $deprecated_orderby;
        }

        $current_bestbuy_bestsell_product_cat = isset( $wp_query->query['bestbuy_bestsell_product_category'] ) ? $wp_query->query['inmid_product_category'] : '';
        $defaults            = array(
            'pad_counts'         => 1,
            'show_counts'        => 1,
            'hierarchical'       => 1,
            'hide_empty'         => 1,
            'show_uncategorized' => 1,
            'orderby'            => 'name',
            'selected'           => $current_inmid_product_cat,
            'menu_order'         => false
        );

        $args = wp_parse_args( $args, $defaults );

        if ( $args['orderby'] == 'order' ) {
            $args['menu_order'] = 'asc';
            $args['orderby']    = 'name';
        }

        $terms = get_terms( 'bestbuy_bestsell_product_category', apply_filters( 'bestbuy_bestsell_product_dropdown_categories_get_terms_args', $args ) );

        if ( ! $terms ) {
            return;
        }

        $output  = "<select name='bestbuy_bestsell_product_cat' class='dropdown_bestbuy_bestsell_product_cat'>";
        $output .= '<option value="" ' .  selected( $current_inmid_product_cat, '', false ) . '>' . __( 'Select a category', TEXTDOMAIN ) . '</option>';
        $output .= wc_walk_category_dropdown_tree( $terms, 0, $args );
        if ( $args['show_uncategorized'] ) {
            $output .= '<option value="0" ' . selected( $current_inmid_product_cat, '0', false ) . '>' . __( 'Uncategorized', TEXTDOMAIN ) . '</option>';
        }
        $output .= "</select>";

        echo $output;
    }
    /**
     * Define custom columns for products
     * @param  array $existing_columns
     * @return array
     */
    function bestbuy_bestsell_product_columns( $existing_columns ) {

        if ( empty( $existing_columns ) && ! is_array( $existing_columns ) ) {
            $existing_columns = array();
        }
        unset( $existing_columns['title'], $existing_columns['comments'], $existing_columns['date'] );

        $columns          = array();
        $columns['cb']    = '<input type="checkbox" />';
        $columns['thumb'] = '<span class="inmid-image tips" data-tip="' . __( 'Image', TEXTDOMAIN ) . '">' . __( 'Image', TEXTDOMAIN ) . '</span>';
        $columns['name']  = __( 'Name', TEXTDOMAIN);
        $columns['cmp']        = '<span class="inmid-cmp tips" data-tip="' . __( 'CMP', TEXTDOMAIN ) . '">' . __( 'CMP', TEXTDOMAIN ) . '</span>';
        $columns['bestbuy_bestsell_product_category']  = __( 'Product Categories', TEXTDOMAIN );
        $columns['bestbuy_bestsell_product_tag']  = __( 'Tags', TEXTDOMAIN );
        $columns['date']         = __( 'Date', TEXTDOMAIN );
        return array_merge( $columns, $existing_columns );
    }

    /**
     * Ouput custom columns for products
     * @param  string $column
     */
    function render_bestbuy_bestsell_product_columns( $column ) {
        global $post;
        $post_meta = get_post_meta( $post->ID );
        //current_market_price
        //print_r( $post_meta ); exit;

        switch ( $column ) {
            case 'thumb' :
                echo '<a href="' . get_edit_post_link( $post->ID ) . '">' . get_image( 'thumbnail' , $post->ID, '' ) . '</a>';
                break;
            case 'name' :
                $edit_link        = get_edit_post_link( $post->ID );
                $title            = _draft_or_post_title();
                $post_type_object = get_post_type_object( $post->post_type );
                $can_edit_post    = current_user_can( $post_type_object->cap->edit_post, $post->ID );
                echo '<strong><a class="row-title" href="' . esc_url( $edit_link ) .'">' . $title.'</a>';
                _post_states( $post );
                echo '</strong>';

                if ( $post->post_parent > 0 ) {
                    echo '&nbsp;&nbsp;&larr; <a href="'. get_edit_post_link( $post->post_parent ) .'">'. get_the_title( $post->post_parent ) .'</a>';
                }

                // Excerpt view
                if ( isset( $_GET['mode'] ) && 'excerpt' == $_GET['mode'] ) {
                    echo apply_filters( 'the_excerpt', $post->post_excerpt );
                }

                // Get actions
                $actions = array();

                $actions['id'] = 'ID: ' . $post->ID;

                if ( $can_edit_post && 'trash' != $post->post_status ) {
                    $actions['edit'] = '<a href="' . get_edit_post_link( $post->ID, true ) . '" title="' . esc_attr( __( 'Edit this item', TEXTDOMAIN ) ) . '">' . __( 'Edit', TEXTDOMAIN ) . '</a>';
                    $actions['inline hide-if-no-js'] = '<a href="#" class="editinline" title="' . esc_attr( __( 'Edit this item inline', TEXTDOMAIN ) ) . '">' . __( 'Quick&nbsp;Edit', TEXTDOMAIN ) . '</a>';
                }
                if ( current_user_can( $post_type_object->cap->delete_post, $post->ID ) ) {
                    if ( 'trash' == $post->post_status ) {
                        $actions['untrash'] = '<a title="' . esc_attr( __( 'Restore this item from the Trash', TEXTDOMAIN ) ) . '" href="' . wp_nonce_url( admin_url( sprintf( $post_type_object->_edit_link . '&amp;action=untrash', $post->ID ) ), 'untrash-post_' . $post->ID ) . '">' . __( 'Restore', TEXTDOMAIN ) . '</a>';
                    } elseif ( EMPTY_TRASH_DAYS ) {
                        $actions['trash'] = '<a class="submitdelete" title="' . esc_attr( __( 'Move this item to the Trash', TEXTDOMAIN ) ) . '" href="' . get_delete_post_link( $post->ID ) . '">' . __( 'Trash', TEXTDOMAIN ) . '</a>';
                    }

                    if ( 'trash' == $post->post_status || ! EMPTY_TRASH_DAYS ) {
                        $actions['delete'] = '<a class="submitdelete" title="' . esc_attr( __( 'Delete this item permanently', TEXTDOMAIN ) ) . '" href="' . get_delete_post_link( $post->ID, '', true ) . '">' . __( 'Delete Permanently', TEXTDOMAIN ) . '</a>';
                    }
                }
                if ( $post_type_object->public ) {
                    if ( in_array( $post->post_status, array( 'pending', 'draft', 'future' ) ) ) {
                        if ( $can_edit_post )
                            $actions['view'] = '<a href="' . esc_url( add_query_arg( 'preview', 'true', get_permalink( $post->ID ) ) ) . '" title="' . esc_attr( sprintf( __( 'Preview &#8220;%s&#8221;', TEXTDOMAIN ), $title ) ) . '" rel="permalink">' . __( 'Preview', TEXTDOMAIN ) . '</a>';
                    } elseif ( 'trash' != $post->post_status ) {
                        $actions['view'] = '<a href="' . get_permalink( $post->ID ) . '" title="' . esc_attr( sprintf( __( 'View &#8220;%s&#8221;', TEXTDOMAIN ), $title ) ) . '" rel="permalink">' . __( 'View', TEXTDOMAIN ) . '</a>';
                    }
                }

                $actions = apply_filters( 'post_row_actions', $actions, $post );

                echo '<div class="row-actions">';

                $i = 0;
                $action_count = sizeof($actions);

                foreach ( $actions as $action => $link ) {
                    ++$i;
                    ( $i == $action_count ) ? $sep = '' : $sep = ' | ';
                    echo '<span class="' . $action . '">' . $link . $sep . '</span>';
                }
                echo '</div>';

                get_inline_data( $post );

                /* Custom inline data for bestbuy_bestsell */
                echo '<div class="hidden" id="bestbuy_bestsell_inline_' . $post->ID . '">
						<div class="menu_order">' . $post->menu_order . '</div>
						<div class="bestbuy_bestsell_price">' . $post_meta['current_market_price'][0] . '</div>
						</div>';
                break;
            case 'cmp' :
                echo get_bestbuy_bestsell_product_price_html( $post_meta ) ? get_bestbuy_bestsell_product_price_html( $post_meta ) : '<span class="na">&ndash;</span>';
                break;
            case 'bestbuy_bestsell_product_category' :
            case 'bestbuy_bestsell_product_tag' :
                if ( ! $terms = get_the_terms( $post->ID, $column ) ) {
                    echo '<span class="na">&ndash;</span>';
                } else {
                    foreach ( $terms as $term ) {
                        $termlist[] = '<a href="' . admin_url( 'edit.php?' . $column . '=' . $term->slug . '&post_type=inmid_product' ) . ' ">' . $term->name . '</a>';
                    }

                    echo implode( ', ', $termlist );
                }
                break;
            default :
                break;
        }
    }
    /**
     * Make columns sortable - https://gist.github.com/906872
     *
     * @param array $columns
     * @return array
     */
    function bestbuy_bestsell_product_sortable_columns( $columns ) {
        $custom = array(
            'name'    => 'title',
            'cmp'    => 'current_market_price'
        );
        return wp_parse_args( $custom, $columns );
    }

    /**
     * Returns the main product image
     *
     * @param string $size (default: 'thumbnail')
     * @return string
     */
    function get_image( $size = 'thumbnail', $post_id, $attr=array() ) {
        $image = '';

        if ( has_post_thumbnail( $post_id ) ) {
            $image = get_the_post_thumbnail( $post_id, $size, $attr );
        } elseif ( ( $parent_id = wp_get_post_parent_id( $post_id ) ) && has_post_thumbnail( $parent_id ) ) {
            $image = get_the_post_thumbnail( $parent_id, $size, $attr );
        } else {
            $image = inmid_product_placeholder_img( $size );
        }
        return $image;
    }
    /**
     * Get the HTML for: CMP ( Current Market Price )
     *
     * @access public
     * @return HTML formatted CMP ( Current Market Price )
     */
    function get_bestbuy_bestsell_product_price_html( $post_meta ){
        if( !empty( $post_meta['current_market_price'][0] ) ){
            $price_html = $post_meta['current_market_price'][0]. '&nbsp'. get_currency();
        }
        return $price_html;
    }

    /**
     * Get the HTML for: currency
     *default:  SEK
     * @access public
     * @return HTML formatted currency
     */
    function get_currency(){
        $default = 'SEK';
        return $default;
    }
    /**
     * Get the placeholder image
     *
     * @access public
     * @return string
     */
    function bestbuy_bestsell_product_placeholder_img( $size = 'thumbnail' ) {
        $dimensions = get_bestbuy_bestsell_product_image_size( $size );
        return apply_filters('bestbuy_bestsell_placeholder_img', '<img src="' . bestbuy_bestsell_placeholder_img_src() . '" alt="' . __( 'Placeholder', TEXTDOMAIN ) . '" width="' . esc_attr( $dimensions['width'] ) . '" class="inmid-placeholder wp-post-image" height="' . esc_attr( $dimensions['height'] ) . '" />', $size, $dimensions );
    }
    /**
     * Get the placeholder image URL for products etc
     *
     * @access public
     * @return string
     */
    function bestbuy_bestsell_placeholder_img_src() {
        return apply_filters( 'bestbuy_bestsell_placeholder_img_src', BESTBUY_BESTSELL_DIR . '/images/placeholder.png' );
    }
    /**
     * Get an image size.
     *
     * @param string $image_size
     * @return array
     */
    function get_bestbuy_bestsell_product_image_size( $image_size ) {
        if ( in_array( $image_size, array( 'thumbnail', 'catalog', 'single' ) ) ) {
            /*
            $size           = get_option( $image_size . '_image_size', array() );
            $size['width']  = isset( $size['width'] ) ? $size['width'] : '300';
            $size['height'] = isset( $size['height'] ) ? $size['height'] : '300';
            $size['crop']   = isset( $size['crop'] ) ? $size['crop'] : 0;*/
            $size['width']  = '300';
            $size['height'] = '300';
            $size['crop']   =  0;

        } else {
            $size = array(
                'width'  => '300',
                'height' => '300',
                'crop'   => 1
            );
        }
        return apply_filters( 'bestbuy_bestsell_get_image_size_' . $image_size, $size );
    }

    /*
    * Bestbuy-bestsell User Authentication
    * Display Authentication Panel
    */
    add_shortcode( 'user_authentication', 'user_authentication_bestbuy_bestsell' );
    function user_authentication_bestbuy_bestsell() {
        ?>
        <div class="flexbox">
            <div class="col box">
                    <h3 class="page-subheading"><?php _e( 'Create an account', TEXTDOMAIN );?> </h3>
                    <div class="form_content clearfix">
                        <p><?php _e( 'Please fill the form to create an account.', TEXTDOMAIN );?> </p>
                        <div style="display:none" id="user_created_success_message" class="alert alert-success"></div>
                        <div style="display:none" id="user_created_error" class="alert alert-danger">
                            <div id="first_name_error_message" ></div>
                            <div id="last_name_error_message" ></div>
                            <div id="invalid_email_error_message" ></div>
                            <div id="user_created_error_message" ></div>
                            <div id="password_error_message" ></div>
                            <div id="company_private_person_error_message" ></div>
                        </div>
                        <div class="form-group">
                            <label for="user_email"><?php _e( 'First Name', TEXTDOMAIN );?></label>
                            <input type="text" value="" name="first_name" id="first_name" class="is_required validate account_input form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="user_email"><?php _e( 'Last Name', TEXTDOMAIN );?></label>
                            <input type="text" value="" name="last_name" id="last_name" class="is_required validate account_input form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="user_email"><?php _e( 'Email address', TEXTDOMAIN );?></label>
                            <input type="email" value="" name="user_email" id="user_email" class="is_required validate account_input form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="user_pass"><?php _e( 'Password', TEXTDOMAIN );?></label>
                            <input type="password" value="" name="user_pass" id="user_pass" data-validate="isPasswd" class="is_required validate account_input form-control">
                        </div>
                        <div class="form-group">
                            <label for="company_private_person">
                                <input type="radio" value="company" name="company_or_private_person" class="company_private_person">
                                <?php _e( 'Company', TEXTDOMAIN );?>
                            </label>

                            <label for="company_private_person"><?php _e( 'Or', TEXTDOMAIN );?>
                                <input type="radio" value="private_person" name="company_or_private_person" class="company_private_person">
                                <?php _e( 'Private Person', TEXTDOMAIN );?>
                            </label>
                        </div>
                        <div class="submit">
                            <input type="hidden" name="redirect_to" id="redirect_to" value="<?php echo wp_get_referer(); ?>" />
                            <button name="user_create" id="user_create" type="submit" class="btn btn-default button button-medium exclusive join-btn">
                                <span>
                                    <i class="icon-user left"></i>
                                    <?php _e( 'Create an account', TEXTDOMAIN );?>
                                </span>
                            </button>
                        </div>
                    </div>
            </div>
            <div class="col box">
                <h3 class="page-subheading"><?php _e( 'Already Have An Account?', TEXTDOMAIN );?></h3>
                <div style="display:none" id="login_success_message" class="alert alert-success"></div>
                <div style="display:none" id="login_error_message" class="alert alert-danger">
                    <div id="login_email_error_message" ></div>
                    <div id="login_password_error_message" ></div>
                    <div id="login_failed_message" ></div>
                </div>
                <div class="form_content clearfix">
                    <div class="form-group">
                        <label for="email"><?php _e( 'Email address', TEXTDOMAIN );?></label>
                        <input type="email" value="" name="login_user_email" id="login_user_email" data-validate="isEmail" class="is_required validate account_input form-control">
                    </div>
                    <div class="form-group">
                        <label for="user_pass"><?php _e( 'Password', TEXTDOMAIN );?></label>
                        <input type="password" value="" name="login_user_pass" id="login_user_pass" data-validate="isPasswd" class="is_required validate account_input form-control">
                    </div>
                    <p class="lost_password form-group">
                        <a title="Recover your forgotten password" href="#"><?php _e( 'Forgot your password?', TEXTDOMAIN );?></a></p>
                    <p class="submit">
                        <input type="hidden" name="login_redirect_to" id="login_redirect_to" value="<?php echo wp_get_referer(); ?>" />
                        <button name="user_create" id="user_create" type="submit" class="btn btn-default button button-medium exclusive login-btn">
                            <span>
                                <i class="icon-lock left"></i>
                                <?php _e( 'Sign in', TEXTDOMAIN );?>
                            </span>
                        </button>
                    </p>
                </div>
            </div>
        </div>
    <?php }
    add_action("wp_ajax_nopriv_bestbuy_bestsell_sign_up_user", "bestbuy_bestsell_sign_up_user_callback");
    add_action("wp_ajax_bestbuy_bestsell_sign_up_user", "bestbuy_bestsell_sign_up_user_callback");
    function bestbuy_bestsell_sign_up_user_callback(){
            $first_name = $_POST['first_name'];
            $last_name = $_POST['last_name'];
            $user_email = $_POST['user_email'];
            $password = $_POST['user_pass'];
            $company_or_private_person = $_POST['company_or_private_person'];
            if( email_exists( $user_email )) {
                echo 2; // 2 = This E-mail already exists
            }else{
                $success_user = wp_create_user( $user_email, $password, $user_email );
                if ( is_wp_error( $success_user ) ) {
                    $error_string = $success_user->get_error_message();
                    _e($error_string, 'bestbuy_bestsell'); // System Error returned by wp_create_user()
                }else{
                    update_user_meta( $success_user, 'company_or_private_person', $company_or_private_person, '' );
                    update_user_meta( $success_user, 'first_name', $first_name, '' );
                    update_user_meta( $success_user, 'last_name', $last_name, '' );
                    $credentials = array(
                        'user_login'    => $user_email,
                        'user_password' => $password,
                        'rememember'    => true
                    );
                    $user = wp_signon( $credentials, false );
                    $userID = $user->ID;
                    wp_set_current_user( $userID, $user_email );
                    wp_set_auth_cookie( $userID, true, false );
                    do_action( 'wp_login', $user_email );
                    echo 1; // 1 = User created successfully
                    exit;
                }
            }
            exit;
    }
    add_action("wp_ajax_nopriv_bestbuy_bestsell_sign_in_user", "bestbuy_bestsell_sign_in_user_callback");
    add_action("wp_ajax_bestbuy_bestsell_sign_in_user", "bestbuy_bestsell_sign_in_user_callback");
    function bestbuy_bestsell_sign_in_user_callback(){
        $user_email = $_POST['user_email'];
        $password = $_POST['user_pass'];
        $credentials = array(
            'user_login'    => $user_email,
            'user_password' => $password,
            'rememember'    => true
        );
        $user = wp_signon( $credentials, false );
        $userID = $user->ID;
        wp_set_current_user( $userID, $user_email );
        wp_set_auth_cookie( $userID, true, false );
        do_action( 'wp_login', $user_email );
        if ( is_user_logged_in() )
        {
            echo 1; // 1 = User LoggedIn successfully
        }
        else
        {
            echo 2; // 1 = User LoggedIn Failed
        }
        exit;
    }
    /** Author: ABU TAHER, Logic-coder IT
     * My Interest Lists
     *
     *  return Logged User Interest Lists
     */
    add_shortcode('all_my_interest', 'all_my_interest_bestbuy_bestsell');
    function all_my_interest_bestbuy_bestsell(){
        get_template_part('views/my_interest', 'list_bestbuybestsell');
    }
    /** Author: ABU TAHER, Logic-coder IT
     * My Account
     *
     * Return Logged User Account Info
     */
    add_shortcode('bestbuy_bestsell_my_account', 'bestbuy_bestsell_my_account');
    function bestbuy_bestsell_my_account(){
        $current_user_id = get_current_user_id();
        $user_info = get_userdata($current_user_id);
        $all_meta_for_user = get_user_meta( $current_user_id );
        ?>
       <div class="my_account">
           <div class="personal_info">
               <h2 class="personal_info_title"><?php _e( 'Personal Data', TEXTDOMAIN );?></h2>
               <div class="form_element">
                   <div style="display:none" id="save_personal_data_success_message" class="alert alert-success save_personal_data_success"></div>
                    <div class="element_group">
                        <div class="element_label">
                            <label><?php _e( 'Gender', TEXTDOMAIN );?></label><span>*</span>
                        </div>
                        <div class="element_value">
                            <select name="gender" id="gender">
                                <option value="Male" <?php if( $all_meta_for_user['gender'][0]=='Male' ){ ?> selected="selected"<?php } ?> ><?php _e( 'Male', TEXTDOMAIN );?></option>
                                <option value="Female" <?php if( $all_meta_for_user['gender'][0]=='Female' ){ ?> selected="selected"<?php } ?>><?php _e( 'Female', TEXTDOMAIN );?></option>
                            </select>
                        </div>
                    </div>
                    <div style="display:none" id="gender_error_message">  </div>
                    <div class="element_group">
                        <div class="element_label">
                            <label><?php _e( 'First Name', TEXTDOMAIN );?></label><span>*</span>
                        </div>
                            <div class="element_value">
                            <input type="text" name="first_name" id="first_name" value="<?php echo $all_meta_for_user['first_name'][0]; ?>" id="first_name" />
                        </div>
                    </div>
                    <div id="first_name_error_message">  </div>
                    <div class="element_group">
                       <div class="element_label">
                           <label><?php _e( 'Last Name', TEXTDOMAIN );?></label><span>*</span>
                       </div>
                       <div class="element_value">
                           <input type="text" name="last_name" id="last_name" value="<?php echo $all_meta_for_user['last_name'][0]; ?>" id="last_name" />
                       </div>
                    </div>
                    <div id="last_name_error_message">  </div>
                    <div class="element_group">
                       <div class="element_label">
                           <label><?php _e( 'Birth Date', TEXTDOMAIN );?></label>
                       </div>
                       <div class="element_value">
                           <div class="day">
                               <select name="day" id="day">
                                   <option value=""><?php _e( 'Day', TEXTDOMAIN );?></option>
                                   <?php
                                   for( $day= 1; $day <= 31; $day++ ){?>
                                       <option value="<?php echo $day;?>" <?php if( $all_meta_for_user['day'][0]==$day ){ ?> selected="selected"<?php } ?> ><?php echo $day;?></option>
                                   <?php }
                                   ?>
                               </select>
                           </div>
                           <div class="month">
                               <select name="month" id="month">
                                   <option value=""><?php _e( 'Month', TEXTDOMAIN );?></option>
                                   <option value="<?php _e( 'January', TEXTDOMAIN );?>" <?php if( $all_meta_for_user['month'][0]=='January' ){ ?> selected="selected"<?php } ?> ><?php _e( 'January', TEXTDOMAIN );?></option>
                                   <option value="<?php _e( 'February', TEXTDOMAIN );?>" <?php if( $all_meta_for_user['month'][0]=='February' ){ ?> selected="selected"<?php } ?>><?php _e( 'February', TEXTDOMAIN );?></option>
                                   <option value="<?php _e( 'March', TEXTDOMAIN );?>" <?php if( $all_meta_for_user['month'][0]=='March' ){ ?> selected="selected"<?php } ?> ><?php _e( 'March', TEXTDOMAIN );?></option>
                                   <option value="<?php _e( 'April', TEXTDOMAIN );?>" <?php if( $all_meta_for_user['month'][0]=='April' ){ ?> selected="selected"<?php } ?> ><?php _e( 'April', TEXTDOMAIN );?></option>
                                   <option value="<?php _e( 'May', TEXTDOMAIN );?>" <?php if( $all_meta_for_user['month'][0]=='May' ){ ?> selected="selected"<?php } ?> ><?php _e( 'May', TEXTDOMAIN );?></option>
                                   <option value="<?php _e( 'June', TEXTDOMAIN );?>" <?php if( $all_meta_for_user['month'][0]=='June' ){ ?> selected="selected"<?php } ?> ><?php _e( 'June', TEXTDOMAIN );?></option>
                                   <option value="<?php _e( 'July', TEXTDOMAIN );?>" <?php if( $all_meta_for_user['month'][0]=='July' ){ ?> selected="selected"<?php } ?> ><?php _e( 'July', TEXTDOMAIN );?></option>
                                   <option value="<?php _e( 'August', TEXTDOMAIN );?>" <?php if( $all_meta_for_user['month'][0]=='August' ){ ?> selected="selected"<?php } ?> ><?php _e( 'August', TEXTDOMAIN );?></option>
                                   <option value="<?php _e( 'September', TEXTDOMAIN );?>" <?php if( $all_meta_for_user['month'][0]=='September' ){ ?> selected="selected"<?php } ?> ><?php _e( 'September', TEXTDOMAIN );?></option>
                                   <option value="<?php _e( 'October', TEXTDOMAIN );?>" <?php if( $all_meta_for_user['month'][0]=='October' ){ ?> selected="selected"<?php } ?> ><?php _e( 'October', TEXTDOMAIN );?></option>
                                   <option value="<?php _e( 'November', TEXTDOMAIN );?>" <?php if( $all_meta_for_user['month'][0]=='November' ){ ?> selected="selected"<?php } ?> ><?php _e( 'November', TEXTDOMAIN );?></option>
                                   <option value="<?php _e( 'December', TEXTDOMAIN );?>" <?php if( $all_meta_for_user['month'][0]=='December' ){ ?> selected="selected"<?php } ?> ><?php _e( 'December', TEXTDOMAIN );?></option>
                               </select>
                           </div>
                           <div class="year">
                               <select name="year" id="year">
                                   <option value=""><?php _e( 'Year', TEXTDOMAIN );?></option>
                                   <?php for ( $year = ( date('Y')-18); $year >= (date('Y')-99); $year--) { ?>
                                    <option value="<?php echo $year;?>" <?php if( $all_meta_for_user['year'][0]==$year ){ ?> selected="selected"<?php } ?> ><?php echo $year;?></option>
                                   <?php } ?>
                               </select>
                           </div>
                       </div>
                    </div>
                   <div id="birth_day_error_message">  </div>
                    <div class="element_group">
                       <div class="element_label">
                           <label><?php _e( 'Mobile Number', TEXTDOMAIN );?></label><span>*</span>
                       </div>
                       <div class="element_value">
                           <input type="text" name="mobile_number" id="mobile_number" value="<?php echo $all_meta_for_user['mobile_number'][0]; ?>" id="mobile_number" />
                       </div>
                    </div>
                    <div id="mobile_number_pattern"> e.g. 07xxxxxxxx </div>
                    <div id="mobile_number_error_message">  </div>
                    <div class="element_group">
                        <div class="element_label">
                        </div>
                       <div class="element_value_company_or_private">
                           <label for="company_private_person">
                               <input type="radio" value="company" name="company_or_private_person" <?php if( $all_meta_for_user['company_or_private_person'][0]=='company' ){ ?> checked <?php } ?> >
                               <?php _e( 'Company', TEXTDOMAIN );?>
                           </label>
                           <label for="company_private_person"><?php _e( 'Or', TEXTDOMAIN );?>
                               <input type="radio" value="private_person" name="company_or_private_person" <?php if( $all_meta_for_user['company_or_private_person'][0]=='private_person' ){ ?> checked <?php } ?> >
                               <?php _e( 'Private Person', TEXTDOMAIN );?>
                           </label>
                       </div>
                    </div>
                    <div id="company_private_person_error_message">  </div>
                    <div class="element_group">
                       <div class="element_asterek_message">
                           <label>* <?php _e( 'Required fields', TEXTDOMAIN );?></label>
                       </div>
                       <div class="element_save_button">
                           <p class="submit">
                               <button name="save_personal_data" id="save_personal_data" type="submit" class="btn btn-default button button-medium exclusive save_personal_data_btn">
                            <span>
                                <i class="icon-lock left"></i>
                                <?php _e( 'Save', TEXTDOMAIN );?>
                            </span>
                               </button>
                           </p>
                       </div>
                    </div>
               </div>
           </div>
           <div class="change_password">
               <h2 class="change_password_title"><?php _e( 'Password', TEXTDOMAIN );?></h2>
               <div class="form_element">
                   <div style="display:none" id="save_password_data_success_message" class="alert alert-success save_personal_data_success"></div>
                   <div style="display:none" id="save_password_data_error_message" class="alert alert-danger save_personal_data_error"></div>
                   <div class="element_group">
                       <div class="element_label">
                           <label><?php _e( 'Old Password', TEXTDOMAIN );?></label><span>*</span>
                       </div>
                       <div class="element_value">
                           <input type="password" name="old_password" id="old_password" value="" id="old_password" />
                       </div>
                   </div>
                   <div id="old_password_error_message">  </div>
                   <div class="element_group">
                       <div class="element_label">
                           <label><?php _e( 'New Password', TEXTDOMAIN );?></label><span>*</span>
                       </div>
                       <div class="element_value">
                           <input type="password" name="new_password" id="new_password" value="" id="new_password" />
                       </div>
                   </div>
                   <div id="new_password_error_message">  </div>
                   <div class="element_group">
                       <div class="element_label">
                           <label><?php _e( 'Confirm Password', TEXTDOMAIN );?></label><span>*</span>
                       </div>
                       <div class="element_value">
                           <input type="password" name="confirm_password" id="confirm_password" value="" id="confirm_password" />
                       </div>
                   </div>
                   <div id="confirm_password_error_message">  </div>
                   <div id="password_not_matched_error_message">  </div>
                   <div class="element_group">
                       <div class="element_asterek_message">
                           <label>* <?php _e( 'Required fields', TEXTDOMAIN );?></label>
                       </div>
                       <div class="element_save_button">
                           <p class="submit">
                               <button name="save_password_data" id="save_password_data" type="submit" class="btn btn-default button button-medium exclusive save_password_data_btn">
                            <span>
                                <i class="icon-lock left"></i>
                                <?php _e( 'Save', TEXTDOMAIN );?>
                            </span>
                               </button>
                           </p>
                       </div>
                   </div>
               </div>
           </div>
       </div>
    <?php }

    add_action("wp_ajax_nopriv_bestbuy_bestsell_save_personal_data", "bestbuy_bestsell_save_personal_data_callback");
    add_action("wp_ajax_bestbuy_bestsell_save_personal_data", "bestbuy_bestsell_save_personal_data_callback");
    function bestbuy_bestsell_save_personal_data_callback(){
        $gender = $_POST['gender'];
        $first_name = $_POST['first_name'];
        $last_name = $_POST['last_name'];
        $birth_day = $_POST['birth_day'];
        $birth_month = $_POST['birth_month'];
        $birth_year = $_POST['birth_year'];
        $mobile_number = $_POST['mobile_number'];
        $company_or_private_person = $_POST['company_or_private_person'];

        $user_id = get_current_user_id();
        update_user_meta( $user_id, 'gender', $gender, '' );
        update_user_meta( $user_id, 'first_name', $first_name, '' );
        update_user_meta( $user_id, 'last_name', $last_name, '' );
        update_user_meta( $user_id, 'birth_day', $birth_day, '' );
        update_user_meta( $user_id, 'birth_month', $birth_month, '' );
        update_user_meta( $user_id, 'birth_year', $birth_year, '' );
        update_user_meta( $user_id, 'mobile_number', $mobile_number, '' );
        update_user_meta( $user_id, 'company_or_private_person', $company_or_private_person, '' );
        echo 1;
        exit;
    }

    add_action("wp_ajax_nopriv_bestbuy_bestsell_save_password_data", "bestbuy_bestsell_save_password_data_callback");
    add_action("wp_ajax_bestbuy_bestsell_save_password_data", "bestbuy_bestsell_save_password_data_callback");
    function bestbuy_bestsell_save_password_data_callback(){
        $old_password = $_POST['old_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];
        $user = wp_get_current_user(); //trace($user);
        $check_password = wp_check_password( $old_password, $user->user_pass, $user->data->ID );
        if($check_password)
        {
            if( !empty( $new_password ) && !empty( $confirm_password ))
            {
                if( $new_password == $confirm_password )
                {
                    $update_data['ID'] = $user->data->ID;
                    $update_data['user_pass'] = $new_password;
                    $uid = wp_update_user( $update_data );
                    if( $uid )
                    {
                        echo 1; // 1 = "The password has been updated successfully";
                    }
                    else
                    {
                        echo 2; // 2 = "Sorry! Failed to update your account details";
                    }
                }
                else
                {
                   // $passupdatemsg = "Confirm password doesn't match with new password";
                }
            }
            else
            {
                ///$passupdatemsg = "Please enter new password and confirm password";
            }
        }
        else
        {
            echo 3; // 3 = "Old Password doesn't match the existing password";
        }
        exit;
    }
    /////////////////////////////////////////////////////////////////////
    /*********  Show interest form for a single product **********/
    /** Author: ABU TAHER, Logic-coder IT
     * Display interest form for single product
     *
     * return interest form for single product
     */
    add_action('bestbuy_bestsell_product_interest_form', 'bestbuy_bestsell_product_interest_form_function');
    function bestbuy_bestsell_product_interest_form_function(){
        global $interest_form, $show_this_div, $interest_start_date_time_as_text, $product_interest_validation_errors, $interest_meta_array, $format_array, $wp_interest_form_data,$product_data, $current_user_id;
        $show_this_div = 0;
        $interest_start_date_time_as_text = '';
        $interest_meta_array = array();
        $format_array = array();
        $product_interest_validation_errors = new WP_Error();
        $current_user_id = get_current_user_id();
        $user_info = get_userdata($current_user_id);
        $all_meta_for_user = get_user_meta( $current_user_id );
        //print_r($all_meta_for_user); exit;
        if($user_info->roles){
            $current_user_role = implode(', ', $user_info->roles) ;
        }
        //////////////////////////////////////////////////////////////
        if( isset( $_POST['save_interest'] ) ){
            $product_attributes = '';
            //$product_attributes = get_field('attributes');
            // Common Field For all types of user
            $product_interest_meta_insert_id = "";
            $product_data['post_title'] = stripslashes_deep(get_the_title());
            $product_data['post_author'] = $current_user_id;
            $product_id = stripslashes_deep(get_the_ID());
            $form_validation_interest = interest_form_validation( );
            if ( sizeof( $product_interest_validation_errors->get_error_messages() ) <= 0 )  {
                $interest_insert = product_interest_insert( $product_id  );
            }else{ /*display_error_message*/
                $show_this_div = 1;
                if( $product_attributes ) {
                    foreach($product_attributes as $product_attribute) {
                        if( isset( $_POST[ $product_attribute['label'] ] ) && !empty( $_POST[ $product_attribute['label'] ] ) ){
                            $interest_meta_array[ $product_attribute['label'] ] = $_POST[ $product_attribute['label'] ] ;
                        }
                    }
                }
            }
        } // End of Post submit checking...
        ////////////////////////////////////////////////////////////

        if( isset( $wp_interest_form_data['interest_start_date'] )  && $wp_interest_form_data['interest_start_date'] ) {
            $interest_start_date_deafult = date('Y-m-d' , $wp_interest_form_data['interest_start_date'] );
        } else {
            $today_date = date('Y-m-d');
            $interest_start_date_deafult = date('Y-m-d', strtotime($today_date. ' + 14 days'));
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
        array_push( $interest_form, array(	'label' => __('Beskriv den önskade produkten' , TEXTDOMAIN  ),
                'name'=>'interest_notes',
                'id'=>'interest_notes',
                'type'=>'textarea',
                'placeholder' =>__('förklaring: egna önskemål om produktens utseende, ålder m.m', TEXTDOMAIN ),
                'value' => $wp_interest_form_data['interest_notes']
            )
        );

        if($all_meta_for_user['company_or_private_person'][0]==='company'){
            $authorative_person_options = array( array( 'label' =>__('Min själv', TEXTDOMAIN ), 'value' => 'My Self' ) );
            if( !$all_meta_for_user['authorative_person_one_first_name'][0] ){
                array_push( $authorative_person_options, array( 'label' =>__('Ny adda', TEXTDOMAIN ), 'value' => 'add_authorative_person' ) );
            }
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
            $checked = '';
            if( isset( $wp_interest_form_data['exclusive_purchase_rights'] ) && $wp_interest_form_data['exclusive_purchase_rights']  ) {
                $checked= 'checked';
            }
            array_push( $interest_form, array(	'label' => __('Give inmid exclusive rights to purchase this product', TEXTDOMAIN ),
                    'name'=>'exclusive_purchase_rights',
                    'id'=>'exclusive_purchase_rights',
                    'type'=>'checkbox',
                    'checked' => $checked
                )
            );
        }
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
                'display' => 'none',
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
    add_shortcode('add_my_product_interest', 'add_my_product_interest_bestbuy_bestsell');
    function add_my_product_interest_bestbuy_bestsell(){
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
                //echo "Not Error";
                /******************** Start : Product Insert HERE ***************/
                $product_data['post_category'] = array( $product_data['post_category'] );
                $product_data['post_type'] = 'product';
                $product_data['post_status']  = 'pending';
                $product_data['post_author']  = get_current_user_id();
                $new_post_id = wp_insert_post( $product_data, $wp_error );
                if( $new_post_id ){
                    $interest_insert = product_interest_insert( $new_post_id  );
                    //$interest_insert = product_interest_insert( $new_post_id , $interest_form_meta );
                    wp_set_post_categories($new_post_id,  $product_data['post_category'], true  );
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

    /*********  Product Form Validation  for Add My Interest **********/
    /** Author: ABU TAHER, Logic-coder IT
     *  return Success/Error Message
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
    function interest_form_validation( ){
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
        //$wp_interest_form_data['subscribe_this_product'] = stripslashes_deep($_POST['subscribe_this_product']);
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
            $interest_start_date_deafult_text = mktime(0, 0, 0, ($interest_start_date_deafult_arr[1]), $interest_start_date_deafult_arr[2], $interest_start_date_deafult_arr[0]);
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
            if( isset( $_POST['exclusive_purchase_rights'] ) && $_POST['exclusive_purchase_rights'] ){
                $wp_interest_form_data['exclusive_purchase_rights'] = 1;
            }
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
        //$product_attributes = get_field('attributes');
        $product_attributes = '';
        $current_user_id = get_current_user_id();
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
                $product_attr[] = array('name' => $product_attribute['label'], 'value' => $_POST[$product_attribute['label'] ] , 'meta_type' => 'product_meta');
            }
        }
        /****************** Start :  Prepare Interest Form Meta *****************/
        if( $_POST['subscribe_this_product']==='others' ){
            $product_attr[] = array('name' => 'subscribe_this_product_others' , 'value' =>$_POST['subscribe_this_product_others'], 'meta_type' => 'interest_form_meta' );
        }else{
            $product_attr[] = array('name' => 'subscribe_this_product' , 'value' =>$_POST['subscribe_this_product'], 'meta_type' => 'interest_form_meta');
        }
        /****************** End :  Prepare Interest Form Meta *****************/
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
    /** Author: ABU TAHER, Logic-coder IT
     * wp_product_interest_insert
     * Param $wp_product_interest_data, $format_array
     * return Interest Insert Id
     */
    function wp_product_interest_insert( $wp_product_interest_data, $format_array  ){
        global $wpdb;
        $wpdb->insert( 'wp_product_interest', $wp_product_interest_data , $format_array=null );
        return $wpdb->insert_id;
    }
    /** Author: ABU TAHER, Logic-coder IT
     * wp_product_interest_update
     *
     * Param $wp_product_interest_data, $format_array
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
     *  wp_product_interest_meta_insert
     *  Param $product_interest_insert_id, $wp_product_interest_data, $interest_attributes
     * return Interest Meta Insert Id
     */
    function wp_product_interest_meta_insert( $product_interest_insert_id, $wp_product_interest_data, $interest_attributes ){
        global $wpdb;
        $wp_product_interest_meta_data = array( 'product_interest_id' => $product_interest_insert_id,
            'user_id'=>$wp_product_interest_data['user_id'],
            'product_id'=> $wp_product_interest_data['product_id'] );
        $format_array = array('%d', '%s', '%d');
        if( $interest_attributes ) {
            foreach($interest_attributes as $interest_attribute) {
                $wp_product_interest_meta_data['meta_type'] = $interest_attribute['meta_type'];
                $wp_product_interest_meta_data['meta_name'] = $interest_attribute['name'];
                $wp_product_interest_meta_data['meta_value'] = $interest_attribute['value'];
                $wp_product_interest_meta_data['add_date'] = date("Y-m-d");
                $format_array = array('%s', '%s', '%s', '%s');
                $wpdb->insert( 'wp_product_interest_meta', $wp_product_interest_meta_data , $format_array );
            }
        }
        return $wpdb->insert_id;
    }
    /** Author: ABU TAHER, Logic-coder IT
     * wp_product_interest_meta_update
     *
     * Param $product_interest_id, $wp_product_interest_data, $product_attributes
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
     * Param $interest_assign_data, $interest_id, $product_id, $user_id
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
     * wp_my_interest_list
     * Param $current_user_id
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
     * Param $current_user_id, $product_interest_id
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
     * Param $current_user_id, $product_interest_id, $check_for
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
     * Param $current_user_id, $product_interest_id
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
     * Param $product_id
     * Return interest Lists  By $product_id
     */
    function get_product_interest_lists( $product_id ){
        global $wpdb, $paged, $max_num_pages, $current_date, $sql_posts_total;
        $paged = ($_GET['paged']) ? $_GET['paged'] : 1;
        $post_per_page = 12;
        $offset = ($paged - 1) * $post_per_page;
        $sql_product_interest = "SELECT SQL_CALC_FOUND_ROWS * FROM wp_product_interest
	JOIN wp_posts ON wp_product_interest.product_id =
	wp_posts.ID AND wp_product_interest.interest_group_id=0
	WHERE wp_product_interest.product_id='".$product_id."' ORDER BY wp_product_interest.interest_end_date ASC  LIMIT $offset,$post_per_page";
        $sql_result = $wpdb->get_results( $sql_product_interest , OBJECT );
        $sql_posts_total = $wpdb->get_var( "SELECT FOUND_ROWS();" );
        $max_num_pages = ceil( $sql_posts_total / $post_per_page );
        return $sql_result;
    }
    /** Author: ABU TAHER, Logic-coder IT
     * get_product_statics
     * Param $product_id
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
     * Param $product_id
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
     * Param $product_id
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
     * Param $product_interest_id
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
     * Param $product_interest_id
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
     * Param $product_id, $group_id
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
     * Param $product_id, $group_id
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
     * Param $product_id, $group_id
     * Return total quantity for failed interest within this group
     */
    function count_interest_failed( $product_id , $group_id ){
        global $wpdb;
        $sql_result = $wpdb->get_results( " SELECT sum( interest_qty ) as total_qty FROM wp_product_interest  WHERE product_id='".$product_id."' AND interest_group_id='".$group_id."' AND interest_campaign_closed= 2 ", OBJECT );
        return $sql_result;
    }
    /** Author: ABU TAHER, Logic-coder IT
     * sum_qty_for_product_interest
     * Param $product_id,  $flag= "not_in_group", $product_interest_id=""
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
     * Param $product_interest_id
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
        return $wpdb->insert_id ? $wpdb->insert_id:"";
    }

    /** Author: ABU TAHER, Logic-coder IT
     * make_as_group_for_interest
     * Param $product_interest_id
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
     * param $group_id =""
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
     * Param $interest_group_id, $action="", $product_interest_id=""
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
     * Param $interest_group_id
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
     * Param $action, $group_id
     */
    function remove_group( $action, $group_id ){
        global $wpdb;
        $delete_rows = $wpdb->query("DELETE FROM wp_interest_group WHERE group_id ='".
            $group_id."' ");
        return $delete_rows;
    }
    /** Author: ABU TAHER, Logic-coder IT
     * rename_group_to
     * Param $group_id, $group_data
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
     * Param $interest_group_id, $action="", $product_interest_id=""
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
     * Param $interest_group_id, $action="", $product_interest_id=""
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
     * Param $price_data
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
     * Param $price_data, $group_price_id
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
     * Param $product_interest_id, $interest_unit_price
     * return Success/Failure Message

    function update_interest_unit_price( $product_interest_id, $interest_unit_price ){
    global $wpdb;
    $price_data = array( "interest_unit_price" => $interest_unit_price );
    $where = array( "product_interest_id" => $product_interest_id);
    $format_array = array( '%f' );
    return $wpdb->update( 'wp_product_interest', $price_data, $where, $format_array = null, 	$where_format = null );
    }*/


    /** Author: ABU TAHER, Logic-coder IT
     * insert_interest_case
     * Param $case_data, $format_array
     * return Success/Failure Message
     */
    function insert_interest_case( $case_data, $format_array ){
        global $wpdb;
        $wpdb->insert( 'wp_interest_group_case', $case_data , $format_array );
        return $wpdb->case_id;
    }


    /*********  Show Product Status : CIQ **********/
    /** Author: ABU TAHER, Logic-coder IT
     * Here Current Interest Quantity ( CIQ ) will show
     * actual interest quantity + bestbuy_bestsell_ciq
     * bestbuy_bestsell_ciq counted here only to show end user but not count as actual deal
     * return CIQ Status Message
     */
    add_action('bestbuy_bestsell_product_status', 'bestbuy_bestsell_product_status_CIQ' , 5 );
    function bestbuy_bestsell_product_status_CIQ( ){
        $product_meta_values = get_post_meta( get_the_ID(), "", "" );
        $ciq_to_display_visitor = 0;
        $inmid_ciq = $product_meta_values['inmid_ciq'][0];
        $current_user_id = get_current_user_id();
        if( isset( $_REQUEST['product_interest_id'] ) && !empty( $_REQUEST['product_interest_id'] ) && !empty( $current_user_id ) ){
            $count_interest_qty = sum_qty_for_product_interest( get_the_ID(), $_REQUEST['product_interest_id'] , $flag= "for_interest_group" );
        }else{
            $count_interest_qty = sum_qty_for_product_interest( get_the_ID(), "", $flag= "in_general" );
        }
        if( $count_interest_qty ){
            $ciq_to_display_visitor = $count_interest_qty[0]->total_qty;
        }
        $ciq_to_display_visitor += $inmid_ciq;

        $ciq_output = '<div class="product_ciq">
			<a id="tool_tip_design" class="tooltip_product_ciq" title="' . __('Current Interest Quantity', TEXTDOMAIN) .'" >
			<span style="font-weight:bold;">'. __('CIQ', TEXTDOMAIN).' : </span>';

        if( $ciq_to_display_visitor ){
            $ciq_output .= $ciq_to_display_visitor;
            $ciq_output .= __(' St' , TEXTDOMAIN );
        }
        $ciq_output .= '</a></div>';
        echo $ciq_output;
    }

    /*********  Show Product Status : PPQ **********/
    /** Author: ABU TAHER, Logic-coder IT
     * Show Price Per Quantity Reached Or Acquiring
     * Must Have at least one ingeneral interest
     * Reached: If at least one group price set for this product
     * Acquiring: If no group price set for this product
     * return PPQ Status Message
     */
    add_action('bestbuy_bestsell_product_status', 'bestbuy_bestsell_product_status_PPQ' , 10 );
    function bestbuy_bestsell_product_status_PPQ( ){
        $current_user_id = get_current_user_id();
        if( isset( $_REQUEST['product_interest_id'] ) && !empty( $_REQUEST['product_interest_id'] ) && !empty( $current_user_id ) ){
            $ppq_data = get_product_ppq( get_the_ID(), $_REQUEST['product_interest_id'], $flag= "for_interest" );
        }else{
            $ppq_data = get_product_ppq( get_the_ID(), "", $flag= "in_general" );
        }
        $ppq_output = '<div class="product_ppq_reached" >
		<a id="tool_tip_design" class="tooltip_product_ppq" title="' . __('Price Per Quantity', TEXTDOMAIN). '" >
		<span style="font-weight:bold;">'.__('PPQ', TEXTDOMAIN). ': </span>';
        if( $ppq_data && $ppq_data[0]->total_qty ){
            $ppq_output .=__('Reached', TEXTDOMAIN);
        }else{
            $ppq_output .=__('Acquiring', TEXTDOMAIN);
        }
        $ppq_output .= '</a></div>';
        echo $ppq_output;
    }

    /*********  Show Product Status : Status = Under Evaluation / MMQ Reached **********/
    /** Author: ABU TAHER, Logic-coder IT
     * Here Product Status Will Show i.e
    1. Campaign Under Evaluation ( If at least one Interest for this product )
    2. Campaign MMQ Reached ( If sum(	interest_qty ) is greater or equal to minimum_target_sells for this product whatever paid or unpaid )
     * return Status = Under Evaluation / MMQ Reached  Message
     */
    add_action('bestbuy_bestsell_product_status', 'bestbuy_bestsell_product_status_MMQ' , 15 );
    function bestbuy_bestsell_product_status_MMQ( ){
        $current_user_id = get_current_user_id();
        if( isset( $_REQUEST['product_interest_id'] ) && !empty( $_REQUEST['product_interest_id'] ) && !empty( $current_user_id  ) ){
            $campaign_mmq_reached = get_campaign_mmq_reached_by_product( get_the_ID(), $_REQUEST['product_interest_id'] , $flag= "for_interest_group" );
        }else{
            $campaign_mmq_reached = get_campaign_mmq_reached_by_product( get_the_ID(), "", $flag= "in_general" );
        }
        $mmq_output = '<div class="product_campaing_status" style="margin:0 20px 10px 0; padding-right:5px; float:left;  background: none repeat-x scroll 0 0;">
		<a id="tool_tip_design" class="tooltip_product_campaing_mmq" title="' . __('Campaign Status. Current condition of our campaign', TEXTDOMAIN). '" >
		<span style="font-weight:bold;">'. __('Status', TEXTDOMAIN) . ': </span>';

        if( $campaign_mmq_reached ){
            $mmq_output .= __('MMQ Reached' , TEXTDOMAIN );
        } else{
            $mmq_output .= __('Under Evaluation' , TEXTDOMAIN );
        }
        $mmq_output .='</a></div>';
        echo $mmq_output;
    }

    /*********  Show Product Status : CMP **********/
    /** Author: ABU TAHER, Logic-coder IT
     * CMP: Current Market Price For this product, if it is set in admin. If not set in admin show nothing
     * return CMP  Message
     */
    add_action('bestbuy_bestsell_product_status_cmp', 'bestbuy_bestsell_product_status_cmp',20  );
    function bestbuy_bestsell_product_status_cmp( ){
        $product_meta_values = get_post_meta( get_the_ID() , "", "" );
        $cmp = $product_meta_values['current_market_price'][0];
        $cmp_output = '';
        if( $cmp ){
            $cmp_output = '<div class="product_cmp" style="margin-top:20px;">
		<a id="tool_tip_design" class="tooltip_product_cmp" title="'. __('Current Market Price. But INMID can give you more lower price if number of interest higher', TEXTDOMAIN). '"  >
		<span style="font-weight:bold;">'. __('CMP', TEXTDOMAIN). ': </span>' . $cmp. 'SEK </a></div>';
        }
        echo $cmp_output;
    }
    /*********  Show Product Status : CMP Button **********/
    /** Author: ABU TAHER, Logic-coder IT
     * CMP: Current Market Price For this product, if it is set in admin. If not set in admin show nothing
     * return CMP Button
     */
    add_action('bestbuy_bestsell_product_status_cmp_button', 'bestbuy_bestsell_product_status_cmp_btn'  );
    function bestbuy_bestsell_product_status_cmp_btn( ){
        $product_meta_values = get_post_meta( get_the_ID() , "", "" );
        $cmp = $product_meta_values['current_market_price'][0];
        $cmp_output_btn = '';
        if( $cmp ){
            $cmp_output_btn = ' <a id="tool_tip_design" class="tooltip_product_cmp" title="' .__('Current Market Price. But INMID can give you more lower price if number of interest higher', TEXTDOMAIN).'" >
		<span style="font-weight:bold;">
			<div style="text-align:center; padding: 20px 50px 0 0; float:left;">
				<input class="add_to_cart_interest_div"  type="button" name="add_to_cart_interest" value="'. __('CMP: ', TEXTDOMAIN). $cmp.' SEK'. ' " >
			</div>
		</a>';
        }
        echo $cmp_output_btn;
    }
    /*********  Show My Interest Stats **********/
    /** Author: ABU TAHER, Logic-coder IT
     * return My Interest Stats
     */
    add_action('my_interest_stats', 'my_interest_stats', 5, 2 );
    function my_interest_stats( $product_interest_id, $post_status ){
        $current_user_id = get_current_user_id();
        if( $product_interest_id ){
            $my_interest_meta_data = wp_my_interest_meta( $current_user_id , $product_interest_id );
            $my_interest_stats_output = '<div class="my_interest_stats" style="margin:0 20px 10px 0; padding-right:5px; float:left;
		background: none repeat-x scroll 0 	0;"> <a id="tool_tip_design" class="tooltip_my_interest_stats" title="' . __('My Interest Stats', TEXTDOMAIN). '" >';

            if( $post_status!='publish' ){
                $my_interest_stats_output .=__('Waiting For Approval');
            }
            else{
                if( !$my_interest_meta_data[0]->interest_campaign_closed && !$my_interest_meta_data[0]->interest_paid ){
                    $my_interest_stats_output .= __('Campaign Under Evaluation' , TEXTDOMAIN );
                }elseif( $my_interest_meta_data[0]->interest_campaign_closed==='2' ){
                    $my_interest_stats_output .= __('Campaign Failed' , TEXTDOMAIN );
                }elseif( $my_interest_meta_data[0]->interest_paid ){
                    $my_interest_stats_output .= __('Campaign On Deal' , TEXTDOMAIN );
                }
            }
            $my_interest_stats_output .='</a></div>';
            echo $my_interest_stats_output;
        }
    }
    /** Author: ABU TAHER, Logic-coder IT
     * get_product_ppq
     * Param $product_id,  $flag= "in_general" means Check a group price set or not for a particular product
     * Param $flag= "for_interest" means Check a group price set or not for a particular interest
     * Param $product_interest_id , When $flag= "in_general" $product_interest_id will be empty
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
    *   bestbuy_bestsell_theme_custom_scripts
    */
    function bestbuy_bestsell_theme_custom_scripts() {
        if ( ! is_admin() ) {
            wp_localize_script( 'function', 'bestbuy_bestsell_sign_up_user_script', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
            wp_enqueue_script('jquery-ui-datepicker');
            wp_enqueue_style('jquery-style', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');
            wp_enqueue_script('jquery');
            wp_register_script('addons_script', BESTBUY_BESTSELL_DIR . '/init/js/jquery.tools.min.js', '');
            wp_enqueue_script('addons_script');
            wp_register_script('inmid-scripts',  BESTBUY_BESTSELL_DIR . '/init/js/bestbuy-bestsell.js', '');
            wp_enqueue_script( 'inmid-scripts');
            wp_enqueue_style( 'inmid-style', BESTBUY_BESTSELL_DIR . '/init/css/bestbuy-bestsell.css' );
        }
        ////////////////////////////////
        wp_enqueue_style( 'datepicker-style', BESTBUY_BESTSELL_DIR . '/init/css/datepicker3.css' );
        wp_enqueue_script( 'jquery', '//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js' );
        wp_enqueue_script( 'general-scripts', BESTBUY_BESTSELL_DIR . '/init/js/scripts.js', array('jquery') );
        wp_enqueue_script( 'select-min-js',  BESTBUY_BESTSELL_DIR . '/init/js/select.min.js', array('jquery'));
        wp_enqueue_script( 'jquery-	validate-min',  BESTBUY_BESTSELL_DIR . '/init/js/jquery.validate.min.js', array('jquery'));
        ////////////////////////////////
    }
    add_action( 'wp_enqueue_scripts', 'bestbuy_bestsell_theme_custom_scripts' );

    function bestbuy_bestsell_theme_custom_wp_admin_scripts() {
        wp_enqueue_script( 'bestbuy-bestsell-admin-scripts', BESTBUY_BESTSELL_DIR . '/init/js/bestbuy-bestsell-admin.js' );
        wp_enqueue_script( 'bestbuy-bestsell-admin-form-validation-scripts', BESTBUY_BESTSELL_DIR . '/init/js/jquery.validate.min.js' );
        wp_enqueue_script( 'bestbuy-bestsell-admin-rool-tip-scripts', BESTBUY_BESTSELL_DIR . '/init/js/jquery.tipTip.min.js' );
        //wp_enqueue_script( 'inmid-admin-meta-box-product-variation-scripts', CODEDROP_DIR . '/init/js/meta-boxes-product-variation.min.js' );
        wp_enqueue_script( 'bestbuy-bestsell-admin-meta-box-product-min-scripts', BESTBUY_BESTSELL_DIR . '/init/js/meta-boxes-product.min.js' );
        //wp_enqueue_script( 'inmid-admin-meta-box-scripts', CODEDROP_DIR . '/init/js/meta-boxes.min.js' );
        wp_enqueue_style( 'bestbuy-bestsell-admin-style', BESTBUY_BESTSELL_DIR . '/bestbuy-bestsell-admin.css' );
        //wp_enqueue_style( 'inmid-admin-product-style', CODEDROP_DIR . '/init/css/admin-product.css' );
    }
    add_action( 'admin_enqueue_scripts', 'bestbuy_bestsell_theme_custom_wp_admin_scripts' );
    /*Add your Hooks , Filters and Theme Support before This*/
}

/******************************************/
/* Add your functions before this */
/******************************************/
?>

