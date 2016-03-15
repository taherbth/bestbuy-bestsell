<?php
/**
 * Inmid Interest Lists Admin Class
 *
 * @author 		Logic-coder IT
 * @category 	Admin
 * @package 	codedrop/Admin
 * @version     1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'Bestbuybestsell_Interest' ) ) :

/**
 * Bestbuybestsell_Interest
 */

class Bestbuybestsell_Interest extends WP_List_Table {

	/** Class constructor */
	public function __construct() { 

		parent::__construct( [
			'singular' => __( 'Interest', TEXTDOMAIN ), //singular name of the listed records
			'plural'   => __( 'Interests',TEXTDOMAIN ), //plural name of the listed records
			'ajax'     => false //does this table support ajax?
		] );

	}
		
	/** Show Action message for Inmid Interest
	*/
	public function show_interest_bulk_action_message( $message = '' ){
		global $user_action; 
		if( isset( $_SESSION['interest_deleted'] ) ){
			$interest_deleted = $_SESSION['interest_deleted']; 
			$no_of_messase = $interest_deleted > 1 ? $interest_deleted. ' Interests' : $interest_deleted. ' Interest'; 
			
			if( 'product-interest-lists' === $user_action || 'add-more-interests' === $user_action || 'add-more-interests-to-confirmed-group' === $user_action ){
				$message = $no_of_messase . __(' Deleted From Interest Lists', TEXTDOMAIN );
			}
			if( 'view-failed-group-details' === $user_action  ){
				$message = $no_of_messase . __(' Deleted From Failed Interest Lists', TEXTDOMAIN );
			}			
			unset( $_SESSION['interest_deleted'] );
			
		}elseif( isset( $_SESSION['group_price_deleted'] ) ){ 
			$group_price_deleted = $_SESSION['group_price_deleted']; 
			$no_of_messase = $group_price_deleted > 1 ? $group_price_deleted. ' Records' : $group_price_deleted. ' Record'; 
			$message = $no_of_messase . __(' Deleted From Group Price Lists', TEXTDOMAIN );
			unset( $_SESSION['group_price_deleted'] ); 
			
		}elseif( isset( $_SESSION['interest_moved_out'] ) ){ 
			$interest_moved_out = $_SESSION['interest_moved_out']; 
			$no_of_messase = $interest_moved_out > 1 ? $interest_moved_out. ' Interests' : $interest_moved_out. ' Interest'; 
			$message = $no_of_messase . __(' Moved Out From Group ', TEXTDOMAIN );
			if( 'view-confirmed-group-details' === $user_action ){
				$message = $no_of_messase . __(' Moved Out From Confirmed Interest Group ', TEXTDOMAIN );
			}			
			unset( $_SESSION['interest_moved_out'] ); 
			
		}elseif( isset( $_SESSION['interest_added_to_group'] ) ){ 
			$interest_added_to_group = $_SESSION['interest_added_to_group']; 
			$no_of_messase = $interest_added_to_group > 1 ? $interest_added_to_group. ' Interests' : $interest_added_to_group. ' Interest'; 
			$message = $no_of_messase . __(' Added To Group ', TEXTDOMAIN );
			if( 'add-more-interests-to-confirmed-group' === $user_action ){
				$message = $no_of_messase . __(' Added To Confirmed Group ', TEXTDOMAIN );
			}	
			unset( $_SESSION['interest_added_to_group'] ); 
		}
	
		if( $message ){
			echo '<div id="message" class="updated fade"><p><strong>' . esc_html( $message ) . '</strong></p></div>';
		}
		$message = ''; 
	}
	
	/** Show Interest Search Message 
	*/
	public function show_interest_search_message( ){
		global $search; 
		if( !empty( $search ) ){
			$message = __( 'Search Results For: ' , TEXTDOMAIN );
			$message .= $search; 
			echo '<div id="interest_search_message" class="interest_search_message"><p><strong>' . esc_html( $message ) . '</strong></p></div><br class="clear">';
		}
	}
	
	/**
	 * Delete a product interest record.
	 *
	 * @param int $id product interest id
	 */
	public static function remove_interest( $id ) {
		global $wpdb;

		$wpdb->delete(
			"{$wpdb->prefix}product_interest",
			[ 'product_interest_id' => $id ],
			[ '%d' ]
		);
		
		$wpdb->delete(
			"{$wpdb->prefix}product_interest_meta",
			[ 'product_interest_id' => $id ],
			[ '%d' ]
		);
	}
/**
	 * Delete a interest_group_price 
	 *
	 * @param int $group_id , $group_price_id
	 */
	public static function remove_group_price( $group_id , $group_price_id ) {
		global $wpdb;
		$wpdb->delete(
			"{$wpdb->prefix}interest_group_price",
			[ 'group_price_id' => $group_price_id ],
			[ '%d' ]
		);		
		
	}

	/** Author ABU TAHER, Logic Coder IT
	 * Move Out Interest From a Group
	 * Param int $id product interest id
	 */
	public static function interest_move_out( $id ) {
		global $wpdb, $user_action ;
		
		$wp_interest_data['interest_group_id'] = 0; 
		if( 'view-confirmed-group-details' === $user_action ){
			$wp_interest_data['interest_confirmed'] =0; 
			$wp_interest_data['interest_confirmation_link_expire'] =0; 
			$wp_interest_data['payment_confirmation_link_expire'] =0; 
		}
		
		//$wpdb->query(" UPDATE wp_product_interest SET interest_group_id =0 WHERE product_interest_id IN($product_interest_id) " );
		return $wpdb->update(
			"{$wpdb->prefix}product_interest", $wp_interest_data, 
			[ 'product_interest_id' => $id ],
			[ '%d' ],
			[ '%d' ]
		);
	}
	
	/** Author ABU TAHER, Logic Coder IT
	 * Add More Interest To a Group
	 * Param $interest_ids separated by comma( , ) product interest ids
	 */
	public static function add_interest_to_group( $interest_ids ){ 
		global $wpdb, $interest_group_id, $user_action;
		$wp_interest_data['interest_group_id'] = $interest_group_id; 
		
		if( 'add-more-interests' === $user_action ){
			return $wpdb->query(	" UPDATE {$wpdb->prefix}product_interest SET interest_group_id ='".$interest_group_id."' WHERE product_interest_id IN( $interest_ids ) " );
		}
		if( 'add-more-interests-to-confirmed-group' === $user_action ){
			return $wpdb->query(	" UPDATE {$wpdb->prefix}product_interest SET interest_group_id ='".$interest_group_id."', interest_confirmed=1 WHERE product_interest_id IN( $interest_ids ) " );
		}
	}
	
	/** Text displayed when no record data is available */
	public function no_items() {
		global $current_tab, $user_action;
		$user_action = empty( $_REQUEST['user_action'] ) ? '' :  $_REQUEST['user_action'] ; 

		switch( $current_tab ){ 
			case 'interest_groups':
			case 'interest_failed_groups':
			case 'interest_confirmed_groups':
				if( 'set-group-price' === $user_action  ){
					_e( 'No Group Price Avaliable.', TEXTDOMAIN );
				}elseif( 'view-group-details' === $user_action || 'view-confirmed-group-details' === $user_action || 'view-failed-group-details' === $user_action || 'add-more-interests-to-confirmed-group' === $user_action || 'add-more-interests' === $user_action ){
					_e( 'No Interest Avaliable.', TEXTDOMAIN );
				}
				else{
					_e( 'No Group Avaliable.', TEXTDOMAIN );
				}				
			break;		
			default: 
				_e( 'No Interest Avaliable.', TEXTDOMAIN );
		}
	}
	/**
	 * Render a column when no column specific method exist.
	 *
	 * @param array $item
	 * @param string $column_name
	 *
	 * @return mixed column
 	*/
	public function column_default( $item, $column_name ) {
		//print_r( $item ); exit;
		global $sql_interest_total, $product_id, $user_action, $build_subtab, $total_qty_column_message; 
		
		if( $item['group_id'] ){
			$count_interest_confirmed = $this->count_interest_confirmed(  $item['product_id'] ,  $item['group_id'] );
			if( 'interest_groups' === $build_subtab ){
				$count_group_interest_qty = $this->sum_qty_for_group_interest(  $item['product_id'] ,  $item['group_id'] );
				
$total_qty_column_message = $count_group_interest_qty[0]['total_qty'] ? $count_group_interest_qty[0]['total_qty'] : '&ndash;'; 
			}elseif( 'interest_failed_groups' === $build_subtab ){
				$count_interest_failed = $this->count_interest_failed( $item['product_id'] ,  $item['group_id'] );
				$total_qty_column_message = $count_interest_failed[0]['total_qty'] ? $count_interest_failed[0]['total_qty'] : '&ndash;'; 
			}
			elseif( 'interest_confirmed_groups' === $build_subtab ){
				$total_qty_column_message = $count_interest_confirmed[0]['total_qty'] ? $count_interest_confirmed[0]['total_qty'] : '&ndash;'; 
			}
		}
		switch ( $column_name ) {
			case 'post_thumbnail':
				echo '<a href="' . get_edit_post_link( $item['ID'] ) . '">' . get_image( 'thumbnail' , $item['ID'], '' ) . '</a>';		
			break;
			
			case 'post_title':
				echo '<strong> <a href="' . get_edit_post_link( $item['ID'] ) . '">' . $item[ $column_name ] . '</a>';	
				if( $item['post_status'] !=="publish" ){ 
						echo ' - <span class="post-state">';
						_e('Waiting for approval', TEXTDOMAIN );
						echo '</span></strong>';	
					} 				
			break;		
			
			case 'group_id':
				echo '<span>';
				echo $item['group_id'];
				echo '</span>';
			break; 
			
			case 'group_name':
				echo '<span style="font-weight:bold;">
				<a style="float:left;" href="group_details.php?group_id='.$item['group_id'].'" >'.$item['group_name'].'</a>';
				echo '</span>';				
			break; 
			
			case 'product_id':
				echo '<span style="font-weight:bold;">
				<a href="' . get_edit_post_link( $item['product_id'] ) . '">' . $item[ 'product_id' ] . '</a>';				
			echo '</span>';				
			break; 
			
			case 'total_interest':
				echo '<span>';
				$product_statistics = $this->get_product_statistics( $item['ID'] ); 
				echo ( $sql_interest_total ? $sql_interest_total : '&ndash;');
				/*if( $item['group_id'] ){
					echo ( $count_group_interest_qty[0]['total_qty'] ? $count_group_interest_qty[0]['total_qty'] : '&ndash;' ); 
				}else{
					$product_statistics = $this->get_product_statistics( $item['ID'] ); 
					echo ( $sql_interest_total ? $sql_interest_total : '&ndash;');
				}*/									
				echo '</span>';
			break;
			
			case 'total_qty':
				echo '<span>';
				if( $item['group_id'] ){					
					//echo ( $count_group_interest_qty[0]['total_qty'] ? $count_group_interest_qty[0]['total_qty'] : '&ndash;' ); 
					echo $total_qty_column_message; 
					}else{
						echo ( $item['total_qty'] ? $item['total_qty'] : '&ndash;');
					}
				
				echo '</span>';
			break;
			
			case 'group_closing_date':
				echo '<span style="font-weight:bold;">';
				 if( $item['group_closing_date']=="asap" ){ _e("asap", TEXTDOMAIN ); } 
				 else  { echo date( "Y-m-d", $item['group_closing_date'] ); } 
				echo '</span>'; 
			break; 
			
			case 'login':
				echo '<span>';
				if( $item['user_id']=="visitor" ) {
					_e("Visitor"); 
				}
				else{ 
					$user_info = get_userdata( $item['user_id'] );
					if( $user_info ){
						foreach( $user_info as $user_data ){
							echo $user_data->user_login;										
						}	
						if( $user_info->roles ){ 
							echo " ( ".$current_user_role = implode(', ', $user_info->roles )." )" ; 
						}
					}
				}
				echo '</span>';
				echo '<a href="'.get_admin_url().'user-edit.php?user_id='.$item['user_id'].'&amp;action=edit">'. get_avatar( $item['user_id'], "150", "", "Not Available" ).' </a>';
				
				if( 'product-interest-lists' === $user_action || 'add-more-interests-to-confirmed-group' === $user_action || 'view-failed-group-details' === $user_action ){
					$delete_nonce = wp_create_nonce( 'remove-interest' );
					$title = '<strong>' . $item['login'] . '</strong>';
					
					$url_param = array( 					
						'product_interest_id' => $item['product_interest_id'], 
						'product_id' => $item['product_id'], 
						'action' => 'remove-interest', 
						'user_action' => $_REQUEST['user_action'], 
						'_wpnonce' => $delete_nonce, 
					);			
					$actions = [
			'delete' => '<a href="' . add_query_arg( $url_param ) . '">'. __("Delete Interest" , TEXTDOMAIN ). ' </a>'
		];
				return $title . $this->row_actions( $actions );
				}
				break;
			
			case 'interest_attributes':
				$interest_meta_details = $this->get_user_product_interest_meta( $item['product_interest_id'], 'product_meta' );				
				if( $interest_meta_details ) {					
					foreach( $interest_meta_details as $interest_meta_data ) {
						if( $interest_meta_data ){   
							echo '<span>'.$interest_meta_data['meta_name'].':</span>' .$interest_meta_data['meta_value']."|";         
							
						}		
					}									
				}else{ 
					echo '&ndash;' ;
				}
				break;
				
			case 'interest_qty':
				echo '<span>'. ( $item['interest_qty'] ? $item['interest_qty']:'&ndash;') .'</span>';
			break;
			
			case 'interest_unit_price': 
			echo '<span id="price_update_message'.$item['product_interest_id'].'"></span><br/>';
			echo '<span>
					<input type="text" name="interest_unit_price'.$item['product_interest_id']. '" id="'.$item['product_interest_id']. '" class="update_interest_unit_price" value="'.$item['interest_unit_price']. '" style="width:110px;" /> 
					<input type="hidden" id="product_interest_id" name="product_interest_id" value="'. $item['product_interest_id'].'" />';
			break;

			case 'interest_start_date':
				echo '<span>';
					if( $item['asa_price_is_reasonable'] ){
						_e("As soon as price is reasonable");
					}else{
						echo ( $item['interest_start_date'] ? date("Y-m-d", $item['interest_start_date'] ):'&ndash;');
					}
				echo '</span>'; 
			break; 
			
			case 'no_of_sells':
				echo '<span>';					
						echo ( $item['no_of_sells'] ? $item['no_of_sells'] : '&ndash;');					
				echo '</span>'; 
			break;
			
			case 'bestbuy_bestsell_price':
				echo '<span>';					
						echo ( $item['bestbuy_bestsell_price'] ? $item['bestbuy_bestsell_price'] : '&ndash;');
				echo '</span>'; 
			break;
			
			case 'vendor_price':
				echo '<span>';					
						echo ( $item['vendor_price'] ? $item['vendor_price'] : '&ndash;');					
				echo '</span>'; 
			break;
			
			case 'shipping_price':
				echo '<span>';					
						echo ( $item['shipping_price'] ? $item['shipping_price'] : '&ndash;');					
				echo '</span>'; 
			break;
			
			case 'add_date':
				echo '<span>';					
						echo ( $item['add_date'] ? $item['add_date'] : '&ndash;');					
				echo '</span>'; 
			break;
			
			case 'interest_end_date': 		
			echo '<span>';
			if( !$item['asa_price_is_reasonable'] ){ 
				echo ( $item['interest_end_date'] ? date( "Y-m-d", $item['interest_end_date'] ) : '&ndash;' ) . '<br/>' ;			 
			} 			
			if( 'product-interest-lists' === $user_action ){					
				echo '<a href="' . admin_url( 'admin.php?page=bestbuy-bestsell-business-settings&tab=interest_groups&product_interest_id='.$item['product_interest_id'] ). '&user_action=make-as-group' . '">' ;
				_e('Make As group', TEXTDOMAIN );
				echo '</a>';			
			}
			echo '</span>'; 	
			break; 
			
			case 'interest_lists_user_actions': 
				echo '<a href="' . admin_url( 'admin.php?page=bestbuy-bestsell-business-settings&tab=' . $_REQUEST['tab'].'&product_id='.$item['ID'] ). '&user_action=product-interest-lists' . '">' ;
				_e('View Details', TEXTDOMAIN );
				echo '</a>';
			break;
				
			case 'product_interest_lists_user_actions': 
			echo '<div>';
			
			$url_param = array( 					
				'product_interest_id' => $item['product_interest_id'], 
				'user_action' => 'view_interest_details',
				'tab' => 'interest_lists',
			);			
			echo '<a href="' . add_query_arg( $url_param ) . '">'. __("View Interest Details" , TEXTDOMAIN ). ' </a>';
			
			//echo '<a style="float:left;" href="' . admin_url( 'admin.php?page=inmid-business-settings&tab=' . $_REQUEST['tab'].'&product_id='.$item['ID'] ). '&action=view_interest_details' . '">'. __("View Details" , TEXTDOMAIN ). ' </a>';
			
			if( 'view-group-details' === $user_action || 'view-confirmed-group-details' === $user_action ){
				$delete_nonce = wp_create_nonce( 'interest-move-out' );
				$url_param = array( 					
						'product_interest_id' => $item['product_interest_id'], 
						'action' => 'interest-move-out', 
						'_wpnonce' => $delete_nonce, 
					);			
				echo '|<a href="' . add_query_arg( $url_param ) . '">'. __("Move Out" , TEXTDOMAIN ). ' </a>';
			}
			elseif( 'add-more-interests' === $user_action  || 'add-more-interests-to-confirmed-group' === $user_action ){
				$url_param = array( 					
						'product_interest_id' => $item['product_interest_id'], 
						'action' => 'add-interest-to-group', 
					);			
				echo '|<a href="' . add_query_arg( $url_param ) . '">'. __("Add To Group" , TEXTDOMAIN ). ' </a>';
			}
			echo '|		
			<a style="cursor:pointer;" class="show_email_to_interester" id="'.$item['product_interest_id']. '">'. __("Send E-mail" , TEXTDOMAIN ). '</a>
			<br/>
			<span id="email_sent_message'. $item['product_interest_id']. '"></span>
			<span id="interester_email_span'. $item['product_interest_id']. '" style="display:none"> 
			<textarea placeholder="Write messae to this interester" name="email_message_to_interester'. $item['product_interest_id']. '" id="email_message_to_interester'. $item['product_interest_id']. '"rows="4" cols="30"></textarea> 
			<input class="send_email_to_interester_class" id="' . $item['product_interest_id'] .'" type="button" name="send_email_to_interester" value="'. __("Send Email" , TEXTDOMAIN ). '" / >	</span>';
			echo '</div>';
			break;
			
			case 'product_interest_groups_user_actions': 
				echo '<div>';
				$url_param = array( 					
					'group_id' => $item['group_id'], 
					'product_id' => $item['product_id'], 
				);			
				if( 'interest_confirmed_groups' === $build_subtab  ){
					$url_param['user_action'] = 'view-confirmed-group-details'; 
					echo '<a href="' . add_query_arg( $url_param ) . '">'. __("View Details" , TEXTDOMAIN ). ' </a>';
					$url_param['user_action'] = 'add-more-interests-to-confirmed-group'; 
					echo '| <a href="' . add_query_arg( $url_param ) . '">'. __("Add More Interests" , TEXTDOMAIN ). ' </a>';
					$url_param['user_action'] = 'set-group-price'; 
					$url_param['tab'] = 'interest_groups'; 
					echo '| <a style="float:inherit;" href="' . add_query_arg( $url_param ) . '">'. __("Set Price" , TEXTDOMAIN ). ' </a>';
				
					break;
				}
				if( 'interest_failed_groups' === $build_subtab  ){
					$url_param['user_action'] = 'view-failed-group-details'; 
					echo '<a href="' . add_query_arg( $url_param ) . '">'. __("View Details" , TEXTDOMAIN ). ' </a>';
					break;
				}
				if( $count_group_interest_qty[0]['total_qty'] ){  
					$url_param['user_action'] = 'view-group-details'; 
					echo '<a style="float:left;" href="' . add_query_arg( $url_param ) . '">'. __("Group Details" , TEXTDOMAIN ). ' </a>|';

				} 
				
				$url_param['user_action'] = 'add-more-interests'; 
				echo '<a href="' . add_query_arg( $url_param ) . '">'. __("Add Interests" , TEXTDOMAIN ). ' </a>';
				 
				if( $count_interest_confirmed[0]['total_qty'] ){ 	
					$url_param['user_action'] = 'interest_confirmed_lists'; 
					echo '| <a style="float:inherit;" href="' . add_query_arg( $url_param ) . '">'. __("Confirmed List" , TEXTDOMAIN ). ' </a>';
					
				}
				$url_param['user_action'] = 'set-group-price'; 
					echo '| <a style="float:inherit;" href="' . add_query_arg( $url_param ) . '">'. __("Set Price" , TEXTDOMAIN ). ' </a>';
				
				if( !$count_group_interest_qty[0]['total_qty'] && !$count_interest_confirmed[0]['total_qty'] ){ 
					$url_param['user_action'] = 'remove_group'; 
					echo '| <a style="float:inherit;" href="' . add_query_arg( $url_param ) . '">'. __("Remove" , TEXTDOMAIN ). ' </a>';
				
				} 
				$url_param['user_action'] = 'edit_group'; 
					echo '| <a style="float:inherit;" href="#' . add_query_arg( $url_param ) . '">'. __("Edit" , TEXTDOMAIN ). ' </a></div>';
			break;
			
			case 'interest_group_price_user_actions':
				$delete_nonce = wp_create_nonce( 'remove-group-price' );
				echo '<div>';				
					
					echo '<a style="float:inherit;" href="' . admin_url( 'admin.php?page=bestbuy-bestsell-business-settings&tab=' . $_REQUEST['tab'].'&group_id='.$item['group_id'] .'&product_id='.$product_id ). '&user_action=set-group-price&action=edit-group-price&group_price_id=' .$item['group_price_id']. '" >'.__("Edit" , TEXTDOMAIN ).' </a>';
				
				echo '|<a style="float:inherit;" href="' . admin_url( 'admin.php?page=bestbuy-bestsell-business-settings&tab=' . $_REQUEST['tab'].'&group_id='.$item['group_id'] .'&product_id='.$product_id ). '&user_action=set-group-price&action=remove-group-price&group_price_id=' .$item['group_price_id'].'&_wpnonce=' .$delete_nonce. '" >'.__("Reomve" , TEXTDOMAIN ).' </a>';
				echo '</div>';
			break;
		//default:
				//return print_r( $item, true ); //Show the whole array for troubleshooting purposes
		}
	}

	/**
	 * Render the bulk edit checkbox
	 *
	 * @param array $item
	 *
	 * @return string
	 */
	function column_cb( $item ) {
		global $user_action; 
		$column_cb = sprintf(
				'<input type="checkbox" name="product_interest_ids[]" value="%s" />', $item['product_interest_id']
			); 
		/*if( 'product-interest-lists' === $user_action || 'view-failed-group-details' === $user_action ){					
			return sprintf(
				'<input type="checkbox" name="bulk-remove-interest[]" value="%s" />', $item['product_interest_id']
			);
		}
		if( 'add-more-interests' === $user_action ){					
			return sprintf(
				'<input type="checkbox" name="bulk-add-interest-to-group[]" value="%s" />', $item['product_interest_id']
			);
		}
		if( 'view-group-details' === $user_action  ){					
			return sprintf( 
				'<input type="checkbox" name="bulk-interest-move-out[]" value="%s" />', $item['product_interest_id']
			);
		}*/
		if( 'set-group-price' === $user_action  ){					
			$column_cb = sprintf( 
				'<input type="checkbox" name="group_price_ids[]" value="%s" />', $item['group_price_id']
			);
		}
		return $column_cb;
	}

	/**
	 * Method for name column
	 *
	 * @param array $item an array of DB data
	 *
	 * @return string
	 */
	function column_name( $item ) {

		$delete_nonce = wp_create_nonce( 'delete_interest' );

		$title = '<strong>' . $item['login'] . '</strong>';

		$actions = [
			'delete' => sprintf( '<a href="?page=%s&action=%s&customer=%s&_wpnonce=%s">Delete</a>', esc_attr( $_REQUEST['page'] ), 'delete', absint( $item['product_interest_id'] ), $delete_nonce )
		];

		return $title . $this->row_actions( $actions );
	}

	/**
	* Interest Lists Columns for Product Interest
	 *  Associative array of columns
	 *
	 * @return array
	 */
	function get_interest_lists_columns() {
		global $build_subtab, $user_action; 
		
		if( 'interest_lists' === $build_subtab ){
				$columns = [
				'post_thumbnail'    => __( 'Image', TEXTDOMAIN ),
				'post_title'    => __( 'Name', TEXTDOMAIN ),
				'total_interest'    => __( 'Total Interest', TEXTDOMAIN ),
				'total_qty'    => __( 'Total Interest Qty', TEXTDOMAIN ),
				'interest_lists_user_actions'    => __( 'Action', TEXTDOMAIN ),
			];
		}		
		if( 'interest_groups' === $build_subtab || 'interest_confirmed_groups' === $build_subtab || 'interest_failed_groups' === $build_subtab ){
				$columns = [
				'group_id'    => __( 'Group ID', TEXTDOMAIN ),
				'group_name'    => __( 'Group Name', TEXTDOMAIN ),
				'product_id'    => __( 'Product ID', TEXTDOMAIN ),
				'total_qty'    => __( 'Total Interest Qty', TEXTDOMAIN ),
				'group_closing_date'    => __( 'Closing Date', TEXTDOMAIN ),
				'product_interest_groups_user_actions'    => __( 'Action', TEXTDOMAIN ),
			];
		}
		if( 'product-interest-lists' === $user_action  || 'view-group-details' === $user_action || 'view-confirmed-group-details' === $user_action || 'view-failed-group-details' === $user_action || 'add-more-interests-to-confirmed-group' === $user_action || 'add-more-interests' === $user_action ){
				$columns = [
				'cb'      => '<input type="checkbox"/>',
				'login'    => __( 'Interest By', TEXTDOMAIN ),
				'interest_attributes'    => __( 'Attributes', TEXTDOMAIN ),
				'interest_qty'    => __( 'Qty', TEXTDOMAIN ),
				'interest_unit_price'    => __( 'Interest Unit Price', TEXTDOMAIN ),
				'interest_start_date'    => __( 'Start Date', TEXTDOMAIN ),
				'interest_end_date'    => __( ' 	End Date', TEXTDOMAIN ),
				'product_interest_lists_user_actions'    => __( 'Action', TEXTDOMAIN ),
			];
		}
		
		elseif( 'set-group-price' === $user_action ){
			$columns = [
				'cb'      => '<input type="checkbox"/>',
				'no_of_sells'    => __( 'No Of Sells', TEXTDOMAIN ),
				'bestbuy_bestsell_price'    => __( 'Bestbuy-bestsell Price', TEXTDOMAIN ),
				'vendor_price'    => __( 'Vendor Price', TEXTDOMAIN ),
				'shipping_price'    => __( 'Shipping Price', TEXTDOMAIN ),
				'add_date'    => __( 'Add Date', TEXTDOMAIN ),
				'interest_group_price_user_actions'    => __( 'Action', TEXTDOMAIN ),
			];
		}
		return $columns;
	}


	/**
	 * Columns to make sortable.
	 *
	 * @return array
	 */
	public function get_interest_lists_sortable_columns() {
		$sortable_columns = array(
			'post_title' => array( 'post_title', true ),
			//'total_qty' => array( 'total_qty', false ),
			//'total_interest' => array( 'total_interest', false ),
			'login' => array( 'login', false ),
			'interest_qty' => array( 'interest_qty', false ),
			'interest_unit_price' => array( 'interest_unit_price', false ),
			'interest_start_date' => array( 'interest_start_date', false ),
			'interest_end_date' => array( 'interest_end_date', false ),
			'group_id' => array( 'group_id', false ),
			'group_name' => array( 'group_name', false ),
			'product_id' => array( 'product_id', false ),
			'group_closing_date' => array( 'group_closing_date', false ),
			'no_of_sells' => array( 'no_of_sells', false ),
			'bestbuy_bestsell_price' => array( 'bestbuy_bestsell_price', false ),
			'vendor_price' => array( 'vendor_price', false ),
			'shipping_price' => array( 'shipping_price', false ),
			'add_date' => array( 'add_date', false ),			
		);
		return $sortable_columns;
	}

	/**
	 * Returns an associative array containing the bulk action
	 *
	 * @return array
	 */
	public function get_bulk_actions( ) {	
		global $build_subtab, $user_action;
		
		switch( $build_subtab ){
			case 'interest_lists':
			case 'interest_groups':
			case 'interest_failed_groups':
			case 'interest_confirmed_groups':
				$actions = [
					
				];
				if( 'product-interest-lists' === $user_action || 'view-failed-group-details' === $user_action ){					
				$actions = [
						'bulk-remove-interest' => 'Delete Interest',
					];
				}
				if( 'add-more-interests' === $user_action || 'add-more-interests-to-confirmed-group' === $user_action ){					
				$actions = [
						'bulk-add-interest-to-group' => 'Add To Group',
						'bulk-remove-interest' => 'Delete Interest',
					];
				}
				if( 'view-group-details' === $user_action || 'view-confirmed-group-details' === $user_action  ){					
				$actions = [
						'bulk-interest-move-out' => 'Move Out',
					];
				}	
				if( 'set-group-price' === $user_action  ){					
				$actions = [
						'bulk-remove-group-price' => 'Delete Group Price',
					];
				}	
			break;
			
			default:
				$actions = [
					'bulk-delete' => 'Delete',
					'Add More' => 'Add More'
				];
		}
	
		return $actions;
	}
	
	/**
	 * Handles data query and filter, sorting, and pagination For Product Interest Lists
	 */
	public function prepare_interest_lists_items(  ) { 
	
		global $sql_posts_total, $build_subtab,  $user_action, $interest_group_id;
		/*if( isset( $_REQUEST['interest_deleted'] ) ){
			$message = __('One Interest Deleted From Interest Lists');
		}*/
		self::show_interest_bulk_action_message(); 
		self::show_interest_search_message(); 
		//exit;
		//$this->_column_headers = $this->get_column_info();
		/**
		 * Init column headers
		 */
		$this->_column_headers = array( $this->get_interest_lists_columns(), array(), $this->get_interest_lists_sortable_columns() );

		/** Process bulk action */
		$this->process_bulk_action();
			
		$per_page     = $this->get_items_per_page( 'product_interest_per_page', 12 );
		$current_page = $this->get_pagenum();
		//$total_items  = self::record_count();
		if( 'product-interest-lists' === $user_action || 'add-more-interests' === $user_action || 'add-more-interests-to-confirmed-group' === $user_action ){
			$this->items = $this->get_product_interest_lists( $per_page , $current_page , '' );
		}elseif( 'view-group-details' === $user_action || 'view-confirmed-group-details' === $user_action || 'view-failed-group-details' === $user_action ){
			$this->items = $this->get_interest_group_details( $per_page, $current_page , $interest_group_id, $group_price_id='', $group_details= '' );
			//print_r( $this->items ); exit;
		}elseif( 'set-group-price' === $user_action ){
			$this->items = $this->get_group_price_list( $per_page , $current_page, $group_price_id="", $interest_group_id );
		}
		elseif( 'interest_lists' === $build_subtab ){
			$this->items = $this->user_product_interest( $per_page , $current_page );
		}elseif( 'interest_groups' === $build_subtab || 'interest_confirmed_groups' === $build_subtab || 'interest_failed_groups' === $build_subtab ){
			$this->items = $this->get_interest_group_info( $per_page , $current_page, $interest_group_id="",$flag = $build_subtab );
		}
		$this->set_pagination_args( [
			'total_items' => $sql_posts_total, //WE have to calculate the total number of items
			'per_page'    => $per_page //WE have to determine how many items to show on a page
		] );
	}
   	
	/** Author: ABU TAHER, Logic-coder IT
	 * get_product_interest_lists
	 * Param $per_page indicates item per page, $page_number indicates current page number, $product_details only for product details regardless of search
	 * Return interest Lists for specific product
	 */ 
	public function get_product_interest_lists( $per_page = 5, $page_number = 1 , $product_details ){ 
		/**********************************************/
		global $wpdb, $sql_posts_total, $inmid_interest_action, $product_id, $search, $where_query, $user_action ;
		$where_query = ''; 
		
		$sql_product_interest_lists = "SELECT SQL_CALC_FOUND_ROWS * FROM {$wpdb->prefix}product_interest, {$wpdb->prefix}posts 
		WHERE {$wpdb->prefix}product_interest.product_id = {$wpdb->prefix}posts.ID AND {$wpdb->prefix}product_interest.interest_group_id=0";
		
		if( $search && empty( $product_details ) ){			
			$sql_product_interest_lists = "SELECT SQL_CALC_FOUND_ROWS * FROM {$wpdb->prefix}product_interest, {$wpdb->prefix}posts, {$wpdb->prefix}users 
			WHERE {$wpdb->prefix}product_interest.product_id = {$wpdb->prefix}posts.ID AND {$wpdb->prefix}product_interest.interest_group_id=0";
			
			$where_query  .= " AND ( ( {$wpdb->prefix}users.user_login LIKE '%" . esc_sql( str_replace( '*', '', $search ) ). "%'
			OR {$wpdb->prefix}users.user_nicename LIKE '%" . esc_sql( str_replace( '*', '', $search ) ). "%' OR {$wpdb->prefix}users.user_email LIKE '%" . esc_sql( str_replace( '*', '', $search ) ). "%' OR {$wpdb->prefix}users.display_name LIKE '%" . esc_sql( str_replace( '*', '', $search ) ). "%' ) AND (  {$wpdb->prefix}product_interest.user_id= {$wpdb->prefix}users.ID )  ) ";
			
			/*$where_query  .= " AND ( ( {$wpdb->prefix}users.user_login LIKE '%" . esc_sql( str_replace( '*', '', $search ) ). "%'
			OR {$wpdb->prefix}users.user_nicename LIKE '%" . esc_sql( str_replace( '*', '', $search ) ). "%' OR {$wpdb->prefix}users.user_email LIKE '%" . esc_sql( str_replace( '*', '', $search ) ). "%' OR {$wpdb->prefix}users.display_name LIKE '%" . esc_sql( str_replace( '*', '', $search ) ). "%' ) AND (  {$wpdb->prefix}product_interest.user_id= {$wpdb->prefix}users.ID )  OR ( {$wpdb->prefix}product_interest.interest_qty LIKE '%" . esc_sql( str_replace( '*', '', $search ) ). "%'
			OR {$wpdb->prefix}product_interest.interest_unit_price LIKE '%" . esc_sql( str_replace( '*', '', $search ) ). "%' 	) ) ";		*/
		}
		/*$sql_product_interest_lists = "SELECT SQL_CALC_FOUND_ROWS * FROM {$wpdb->prefix}product_interest
		JOIN {$wpdb->prefix}posts ON {$wpdb->prefix}product_interest.product_id = 				
		{$wpdb->prefix}posts.ID AND {$wpdb->prefix}product_interest.interest_group_id=0 ";
		
		if( $search ){
			$where_query  .= " WHERE {$wpdb->prefix}posts.post_title LIKE '%" . esc_sql( str_replace( '*', '', $search ) ) . "%' OR {$wpdb->prefix}posts.post_name LIKE '%" . esc_sql( str_replace( '*', '', $search ) ). "%' OR ( ( {$wpdb->prefix}users.user_login LIKE '%" . esc_sql( str_replace( '*', '', $search ) ). "%'
			OR {$wpdb->prefix}users.user_nicename LIKE '%" . esc_sql( str_replace( '*', '', $search ) ). "%' OR {$wpdb->prefix}users.user_email LIKE '%" . esc_sql( str_replace( '*', '', $search ) ). "%' OR {$wpdb->prefix}users.display_name LIKE '%" . esc_sql( str_replace( '*', '', $search ) ). "%' ) AND (  {$wpdb->prefix}product_interest.user_id= {$wpdb->prefix}users.ID )			
			) ";		
		}*/
		
		if( 'product-interest-lists' === $user_action && !empty( $product_id ) ){
			$where_query  .= " AND {$wpdb->prefix}product_interest.product_id='".$product_id."' "; 

			/*if( !empty( $where_query ) ){
				$where_query  .= " AND {$wpdb->prefix}product_interest.product_id='".$product_id."' "; 
			}
		else{
					$where_query  .= " WHERE {$wpdb->prefix}product_interest.product_id='".$product_id."' "; 
				}*/			
		}
		if( !empty( $where_query ) ){
			$sql_product_interest_lists  .= $where_query; 
		}
	
		if ( ! empty( $_REQUEST['orderby'] ) && 'login' === $_REQUEST['orderby'] ) {
			$sql_product_interest_lists .= " ORDER BY user_id";
			$sql_product_interest_lists .= ! empty( $_REQUEST['order'] ) ? ' ' . esc_sql( $_REQUEST['order'] ) : ' ASC';
		}
		elseif ( ! empty( $_REQUEST['orderby'] ) ) {
			$sql_product_interest_lists .= ' ORDER BY ' . esc_sql( $_REQUEST['orderby'] );
			$sql_product_interest_lists .= ! empty( $_REQUEST['order'] ) ? ' ' . esc_sql( $_REQUEST['order'] ) : ' ASC';
		}else{
			$sql_product_interest_lists .= " ORDER BY {$wpdb->prefix}product_interest.interest_end_date ASC";
		}
		
		$sql_product_interest_lists .= " LIMIT $per_page";
		$sql_product_interest_lists .= ' OFFSET ' . ( $page_number - 1 ) * $per_page;
		$sql_product_interest_lists_result = $wpdb->get_results( $sql_product_interest_lists , 'ARRAY_A' );
		$sql_posts_total = $wpdb->get_var( "SELECT FOUND_ROWS();" ); 
		//print_r( $sql_product_interest_result ); exit;
		//$max_num_pages = ceil($sql_posts_total / $post_per_page);
		return $sql_product_interest_lists_result;
	}
	/** Author: ABU TAHER, Logic-coder IT
	 *  user_product_interest
	 *  Param $per_page indicates item per page, $page_number indicates current page number
	 *  Return interest Lists
	 */ 
	public function user_product_interest( $per_page = 5, $page_number = 1 ){ 
		/**********************************************/
		global $wpdb, $sql_posts_total, $inmid_interest_action, $product_id, $search, $where_query;
		$where_query = ''; 
		$sql_product_interest = "SELECT SQL_CALC_FOUND_ROWS *,  sum( interest_qty ) as total_qty, {$wpdb->prefix}posts.ID FROM {$wpdb->prefix}product_interest
		JOIN {$wpdb->prefix}posts ON {$wpdb->prefix}product_interest.product_id = 				
		{$wpdb->prefix}posts.ID AND {$wpdb->prefix}product_interest.interest_group_id=0 ";
		
		if( $search ){
			$where_query  .= " WHERE {$wpdb->prefix}posts.post_title LIKE '%" . esc_sql( str_replace( '*', '', $search ) ) . "%' OR {$wpdb->prefix}posts.post_name LIKE '%" . esc_sql( str_replace( '*', '', $search ) ). "%' ";		
		}
		if( 'product-interest-lists'=== $user_action && !empty($product_id) ){
			if( !empty( $where_query ) ){
				$where_query  .= " AND {$wpdb->prefix}product_interest.product_id='".$product_id."' "; 
			}else{
					$where_query  .= " WHERE {$wpdb->prefix}product_interest.product_id='".$product_id."' "; 
				}			
		}
		if( !empty( $where_query ) ){
			$sql_product_interest  .= $where_query; 
		}
		
		$sql_product_interest  .= " GROUP BY {$wpdb->prefix}posts.ID " ;	
	
		if ( ! empty( $_REQUEST['orderby'] ) ) {
			$sql_product_interest .= ' ORDER BY ' . esc_sql( $_REQUEST['orderby'] );
			$sql_product_interest .= ! empty( $_REQUEST['order'] ) ? ' ' . esc_sql( $_REQUEST['order'] ) : ' ASC';
		}else{
			$sql_product_interest .= " ORDER BY {$wpdb->prefix}product_interest.interest_end_date ASC";
		}
		
		$sql_product_interest .= " LIMIT $per_page";
		$sql_product_interest .= ' OFFSET ' . ( $page_number - 1 ) * $per_page;
		$sql_product_interest_result = $wpdb->get_results( $sql_product_interest , 'ARRAY_A' );
		$sql_posts_total = $wpdb->get_var( "SELECT FOUND_ROWS();" ); 
		//exit;
		//print_r( $sql_product_interest_result ); exit;
		//$max_num_pages = ceil($sql_posts_total / $post_per_page);
		return $sql_product_interest_result;
	}

	/** Author: ABU TAHER, Logic-coder IT
 	* get_product_statics
 	* Param $product_id
 	* Return product statics By $product_id
 	*/
	public function get_product_statistics( $product_id ){
		global $wpdb, $sql_interest_total;	
		$sql_prod_statistics = "SELECT SQL_CALC_FOUND_ROWS * FROM {$wpdb->prefix}product_interest WHERE product_id='".$product_id."' AND {$wpdb->prefix}product_interest.interest_group_id=0"; 
		$sql_result = $wpdb->get_results( $sql_prod_statistics ,  'ARRAY_A' );
		$sql_interest_total = $wpdb->get_var( "SELECT FOUND_ROWS();" ); 
		return $sql_result;
	}

	/** Author: ABU TAHER, Logic-coder IT
	 * get_interest_group_info
	 * Param $group_id ="" , $flag indicates which group info i.e failed , success etc
	 * Return interest_group_info
	 */
	public function get_interest_group_info( $per_page = 5, $page_number = 1, $group_id="" , $flag ){
		
		global $wpdb , $sql_posts_total , $where_query, $search;
		$where_query =''; 
		
		if( $group_id ){
			$sql_interest_group = " SELECT SQL_CALC_FOUND_ROWS * FROM {$wpdb->prefix}interest_group inte_grp, {$wpdb->prefix}product_interest prod_inte ";
			$where_query .= " WHERE inte_grp.group_id='".$group_id."' AND  inte_grp.product_id=prod_inte.product_id AND prod_inte.interest_group_id=0";
		}else{
		  $sql_interest_group = " SELECT SQL_CALC_FOUND_ROWS * FROM {$wpdb->prefix}interest_group";
			//ORDER BY group_id DESC 
		  if( 'interest_failed_groups' === $flag ){
			$where_query .= " WHERE {$wpdb->prefix}interest_group.group_id IN( SELECT interest_group_id FROM {$wpdb->prefix}product_interest WHERE {$wpdb->prefix}product_interest.interest_campaign_closed=2)";
		}
		
		if( 'interest_confirmed_groups' === $flag ){
			$where_query .= " WHERE {$wpdb->prefix}interest_group.group_id IN( SELECT interest_group_id FROM {$wpdb->prefix}product_interest WHERE {$wpdb->prefix}product_interest.interest_confirmed= 1 )";
		 }
	
	//SELECT * FROM wp_interest_group WHERE wp_interest_group.group_id IN( SELECT interest_group_id FROM wp_product_interest WHERE wp_product_interest.interest_campaign_closed= 2) ORDER BY wp_interest_group.group_id DESC 
	
	
		}
		
		if( $search ){
			if( !empty( $where_query ) ){
					$where_query  .= " AND {$wpdb->prefix}interest_group.group_name LIKE '%" . esc_sql( str_replace( '*', '', $search ) ) . "%' ";	
			}else{
					$where_query  .= " WHERE {$wpdb->prefix}interest_group.group_name LIKE '%" . esc_sql( str_replace( '*', '', $search ) ) . "%' ";	
			}
		}

		if( !empty( $where_query ) ){
			$sql_interest_group  .= $where_query; 
		}		
	
		if ( ! empty( $_REQUEST['orderby'] ) ) {
			$sql_interest_group .= ' ORDER BY ' . esc_sql( $_REQUEST['orderby'] );
			$sql_interest_group .= ! empty( $_REQUEST['order'] ) ? ' ' . esc_sql( $_REQUEST['order'] ) : ' ASC';
		}else{ 
			$sql_interest_group .= " ORDER BY {$wpdb->prefix}interest_group.group_id DESC";
		}
		//echo $sql_interest_group;
		$sql_interest_group .= " LIMIT $per_page";
		$sql_interest_group .= ' OFFSET ' . ( $page_number - 1 ) * $per_page;		
		$sql_interest_group_result = $wpdb->get_results( $sql_interest_group , 'ARRAY_A' );
		$sql_posts_total = $wpdb->get_var( "SELECT FOUND_ROWS();" ); 
		//echo $sql_interest_group; 
		return $sql_interest_group_result;	
	}
	/** Author: ABU TAHER, Logic-coder IT
	 * sum_qty_for_group_interest
	 * Param $product_id, $group_id
	 * Return total quantity for this group
	 */
	public function sum_qty_for_group_interest( $product_id , $group_id ){
		global $wpdb;
		$sql_result = $wpdb->get_results( " SELECT sum( interest_qty ) as total_qty FROM {$wpdb->prefix}product_interest  WHERE product_id='".$product_id."' AND interest_group_id='".$group_id."' AND interest_confirmed= 0 AND interest_campaign_closed= 0", 'ARRAY_A' );
		return $sql_result;
	}
	/** Author: ABU TAHER, Logic-coder IT
	 * count_interest_confirmed
	 * Param $product_id, $group_id
	 * Return total quantity for confirmed interest within this group
	 */
	public function count_interest_confirmed( $product_id , $group_id ){ 
		global $wpdb;
		$sql_result = $wpdb->get_results( " SELECT sum( interest_qty ) as total_qty FROM {$wpdb->prefix}product_interest  WHERE product_id='".$product_id."' AND interest_group_id='".$group_id."' AND interest_confirmed= 1 ", 'ARRAY_A' );
		return $sql_result;
	}

	/** Author: ABU TAHER, Logic-coder IT
	 * count_interest_failed
	 *@Param $product_id, $group_id
	 * Return total quantity for failed interest within this group
	 */
	function count_interest_failed( $product_id , $group_id ){
		global $wpdb;
		$sql_result = $wpdb->get_results( " SELECT sum( interest_qty ) as total_qty FROM {$wpdb->prefix}product_interest  WHERE product_id='".$product_id."' AND interest_group_id='".$group_id."' AND interest_campaign_closed= 2 ", 'ARRAY_A' );
		return $sql_result;
	}
	/** Author: ABU TAHER, Logic-coder IT
	 * Make Interest groups
	 * Return Success / Failure message
	 */
	public function make_interest_groups(){
		global $user_action;
		$success = 0;
		$product_interest_id = $_REQUEST['product_interest_id'] ? $_REQUEST['product_interest_id'] : "";
		if( isset( $user_action ) && 'group_by_attributes' === $user_action ){
				$group_id = $_GET['group_id']? $_GET['group_id']:"";
				$interest_meta_details = get_user_product_interest_meta( $product_interest_id );
				$interest_meta_array = array();
				if( $interest_meta_details ) {
					foreach( $interest_meta_details as $interest_meta_data ) {
						if( $interest_meta_data ){
							$interest_meta_array[$interest_meta_data->meta_name] = $interest_meta_data->meta_value;
						}
					}
			}
			//print_r( $interest_meta_array ); exit;
		}else{
			$make_group_name_info = $this->make_group_name( $product_interest_id );
		}
	}
	/** Author: ABU TAHER, Logic-coder IT
	 * make_group_name
	 * Param $product_interest_id
	 * Return virtual group name for $product_interest_id with a connection of "interest_end_date"
	 */
	public function make_group_name( $product_interest_id ){
		global $wpdb, $current_date, $sql_posts_total, $group_name, $product_id;
		$sql_result = $wpdb->get_results( " SELECT * FROM {$wpdb->prefix}product_interest WHERE product_interest_id='".
		$product_interest_id."' AND interest_group_id=0", 'ARRAY_A' );
		if( $sql_result ){
			if( $sql_result[0]['interest_end_date'] ){
				$wp_group_data['group_name'] = date("F j, Y", $sql_result[0]['interest_end_date'])."_".date("s")."_".$sql_result[0]['product_id'];
				$wp_group_data['group_closing_date'] = $sql_result[0]['interest_end_date'];
			}elseif( $sql_result[0]['asa_price_is_reasonable']  ){
				$wp_group_data['group_name'] = "asap_".date("s")."_". $sql_result[0]['product_id'];
				$wp_group_data['group_closing_date'] = "asap";
			}
			$wp_group_data['product_id'] = $sql_result[0]['product_id'];
			$format_array = array('%s', '%s', '%d');
			$wp_group_data['add_date'] = date("Y-m-d");
			array_push($format_array, '%s');
			$wpdb->insert( "{$wpdb->prefix}interest_group", $wp_group_data , $format_array );
		}

		if( $wpdb->insert_id ){

			if( $sql_result[0]['interest_end_date'] ){
				$sql_group_result = $wpdb->get_results( " SELECT SQL_CALC_FOUND_ROWS * FROM {$wpdb->prefix}product_interest prod_int, {$wpdb->prefix}posts
				WHERE prod_int.interest_start_date <= ( SELECT interest_end_date FROM {$wpdb->prefix}product_interest WHERE
				product_interest_id='".$product_interest_id."' ) AND prod_int.product_id=( SELECT product_id FROM
				{$wpdb->prefix}product_interest WHERE product_interest_id='".$product_interest_id."' ) AND prod_int.product_id =
				{$wpdb->prefix}posts.ID AND {$wpdb->prefix}posts.post_status='publish' AND prod_int.asa_price_is_reasonable!=1 AND prod_int.interest_group_id=0", 'ARRAY_A' );
			}elseif( $sql_result[0]['asa_price_is_reasonable']  ){
				$sql_group_result = $wpdb->get_results( " SELECT SQL_CALC_FOUND_ROWS * FROM {$wpdb->prefix}product_interest prod_int, {$wpdb->prefix}posts
				WHERE prod_int.product_id=( SELECT product_id FROM {$wpdb->prefix}product_interest WHERE product_interest_id='".$product_interest_id."' ) AND prod_int.product_id =
				{$wpdb->prefix}posts.ID AND {$wpdb->prefix}posts.post_status='publish' AND prod_int.asa_price_is_reasonable=1 AND prod_int.interest_group_id=0", 'ARRAY_A' );
			}
			$sql_total_interest = $wpdb->get_var( "SELECT FOUND_ROWS();" );
			if( $sql_group_result ){
				$wp_product_interest_data['interest_group_id'] = $wpdb->insert_id;
				foreach( $sql_group_result as $sql_group_result_data ) {
					$where = array( "product_interest_id"=> $sql_group_result_data['product_interest_id'] );
					$where_format = array();
					$wpdb->update( "{$wpdb->prefix}product_interest", $wp_product_interest_data, $where, $format_array = null, $where_format = null );
				}
				/*$wp_interest_group_data['total_interest_qty'] = $count_interest_qty;
				$where = array( "group_id"=>$wp_product_interest_data['interest_group_id'] );
				$where_format = array();
				$wpdb->update( 'wp_interest_group', $wp_interest_group_data, $where, $format_array = null, $where_format = null );	*/
			}
		}
		return $wpdb->insert_id ? $wpdb->insert_id : "";
	}

	/** Author: ABU TAHER, Logic-coder IT
	 * sum_qty_for_product_interest
	 *@Param $product_id,  $flag= "not_in_group", $product_interest_id=""
	 * Return total quantity By  $product_id
	 */
	public function sum_qty_for_product_interest( $product_id ,$product_interest_id, $flag ){
		global $wpdb;
		if( $flag=="not_in_group" ){
				$sql_result = $wpdb->get_results( " SELECT sum( interest_qty ) as total_qty FROM {$wpdb->prefix}product_interest  WHERE product_id='".$product_id."' AND interest_group_id=0",  'ARRAY_A' );
		}elseif( $flag=="in_general" ){
				$sql_result = $wpdb->get_results( " SELECT sum( interest_qty ) as total_qty FROM {$wpdb->prefix}product_interest  WHERE product_id='".$product_id."' AND interest_campaign_closed=0",  'ARRAY_A' );
		}elseif( $flag=="for_interest_group" ){
						$sql_result = $wpdb->get_results( " SELECT SQL_CALC_FOUND_ROWS * ,  sum( interest_qty )
						as total_qty FROM {$wpdb->prefix}product_interest WHERE interest_group_id = ( SELECT interest_group_id from {$wpdb->prefix}product_interest WHERE product_interest_id='".$product_interest_id."' AND interest_campaign_closed=0 ) ", 'ARRAY_A' );

		}
		return $sql_result;
	}

	/** Author: ABU TAHER, Logic-coder IT
	 * update_group_price
	 * Param $price_data, $group_price_id
	 * return Success/Failure Message
	 */
	public function update_group_price( $price_data, $group_price_id ){
		global $wpdb;
		$where = array( "group_price_id" => $group_price_id );
		$format_array = array('%d', '%d', '%f', '%f', '%f');
		return $wpdb->update( "{$wpdb->prefix}interest_group_price", $price_data, $where, $format_array = null, $where_format = null );
		//return $wpdb->insert_id ? $wpdb->insert_id: "";
	}

	/** Author: ABU TAHER, Logic-coder IT
	 * set_group_price
	 * Param $price_data
	 * return Success/Failure Message
	 */
	public function set_group_price( $price_data ){
		global $wpdb;
		$format_array = array('%d', '%d', '%f', '%f', '%f',  '%s');
		$wpdb->insert( 'wp_interest_group_price', $price_data , $format_array );
		return $wpdb->insert_id;
	}

	/** Author: ABU TAHER, Logic-coder IT
	 * get_group_price_by_id
	 * Param $group_id, $group_price_id
	 * Return Group Price for specific ID
	 */

	public function get_group_price_by_id( $group_id, $group_price_id ){
		global $wpdb, $price_data_by_id, $sql_total_price_list;
		$where_query = '';

		$sql_group_price = "SELECT SQL_CALC_FOUND_ROWS * FROM {$wpdb->prefix}interest_group_price";

		if( $group_price_id ){
			$where_query .= " WHERE group_price_id='".$group_price_id."' ";
		}elseif( $group_id ){
			$where_query .= " WHERE group_id='".$group_id."' ";
		}

		if( !empty( $where_query ) ){
			$sql_group_price  .= $where_query;
		}
		if ( ! empty( $_REQUEST['orderby'] ) ) {
			$sql_group_price .= ' ORDER BY ' . esc_sql( $_REQUEST['orderby'] );
			$sql_group_price .= ! empty( $_REQUEST['order'] ) ? ' ' . esc_sql( $_REQUEST['order'] ) : ' ASC';
		}else{
			$sql_group_price .= " ORDER BY {$wpdb->prefix}interest_group_price.no_of_sells ASC";
		}

		//$sql_group_price_result = $wpdb->get_results( $sql_group_price , 'ARRAY_A' );
		$price_data_by_id = $wpdb->get_results( $sql_group_price , 'ARRAY_A' );
		$sql_total_price_list = $wpdb->get_var( "SELECT FOUND_ROWS();" );
		return $price_data_by_id;
	}
	/** Author: ABU TAHER, Logic-coder IT
	 *  get_group_price_list
	 * Param $group_price_id="", $group_id, $action=""
	 * Return Group Price List
	 */
	public function get_group_price_list( $per_page = 5, $page_number = 1 , $group_price_id="", $group_id ){
		global $wpdb, $sql_posts_total, $sql_total_price_list, $price_data_by_id, $where_query, $search, $user_action, $action ;

		$where_query = '';

		$group_price_sql = "SELECT SQL_CALC_FOUND_ROWS * FROM {$wpdb->prefix}interest_group_price ";

		if( $group_id ){
			$where_query .= " WHERE group_id='".$group_id."' ";
		}
		if( $search ){
			if( !empty( $where_query ) ){
				$where_query  .= " AND {$wpdb->prefix}interest_group_price.no_of_sells LIKE '%" . esc_sql( str_replace( '*', '', $search ) ) . "%' OR {$wpdb->prefix}interest_group_price.bestbuy_bestsell_price LIKE '%" . esc_sql( str_replace( '*', '', $search ) ) . "%'  OR {$wpdb->prefix}interest_group_price.vendor_price LIKE '%" . esc_sql( str_replace( '*', '', $search ) ) . "%' OR {$wpdb->prefix}interest_group_price.shipping_price LIKE '%" . esc_sql( str_replace( '*', '', $search ) ) . "%' ";
			}else{
				$where_query  .= " WHERE {$wpdb->prefix}interest_group_price.no_of_sells LIKE '%" . esc_sql( str_replace( '*', '', $search ) ) . "%' OR {$wpdb->prefix}interest_group_price.bestbuy_bestsell_price LIKE '%" . esc_sql( str_replace( '*', '', $search ) ) . "%'  OR {$wpdb->prefix}interest_group_price.vendor_price LIKE '%" . esc_sql( str_replace( '*', '', $search ) ) . "%' OR {$wpdb->prefix}interest_group_price.shipping_price LIKE '%" . esc_sql( str_replace( '*', '', $search ) ) . "%' ";
			}
		}
		if( !empty( $where_query ) ){
			$group_price_sql  .= $where_query;
		}
		if ( ! empty( $_REQUEST['orderby'] ) ) {
			$group_price_sql .= ' ORDER BY ' . esc_sql( $_REQUEST['orderby'] );
			$group_price_sql .= ! empty( $_REQUEST['order'] ) ? ' ' . esc_sql( $_REQUEST['order'] ) : ' ASC';
		}else{
			$group_price_sql .= " ORDER BY {$wpdb->prefix}interest_group_price.no_of_sells ASC";
		}
		$group_price_sql .= " LIMIT $per_page";
		$group_price_sql .= ' OFFSET ' . ( $page_number - 1 ) * $per_page;
		$group_price_sql_result = $wpdb->get_results( $group_price_sql , 'ARRAY_A' );
		$sql_posts_total = $wpdb->get_var( "SELECT FOUND_ROWS();" );
		//$sql_total_price_list = $sql_posts_total;
		//echo $group_price_sql;
		return $group_price_sql_result;
	}

	/** Author: ABU TAHER, Logic-coder IT
	 * get_group_info
	 * Param $interest_group_id
	 * Return Group Details
	 */
	public function get_group_info( $interest_group_id ){
		global $wpdb, $sql_posts_total;
		$sql_result = $wpdb->get_results( "SELECT SQL_CALC_FOUND_ROWS * FROM {$wpdb->prefix}interest_group inte_grp , {$wpdb->prefix}posts WHERE ( inte_grp.group_id='".$interest_group_id."'  AND  inte_grp.product_id = {$wpdb->prefix}posts.ID AND {$wpdb->prefix}posts.post_status='publish') ", 'ARRAY_A' );
		//$sql_result = $wpdb->get_results( "SELECT SQL_CALC_FOUND_ROWS * FROM wp_product_interest prod_inte, wp_interest_group inte_grp,wp_posts WHERE ( prod_inte.interest_group_id=inte_grp.group_id AND inte_grp.group_id='".$interest_group_id."' ) AND ( inte_grp.product_id=wp_posts.ID AND wp_posts.post_status='publish') ", OBJECT );
		$sql_posts_total = $wpdb->get_var( "SELECT FOUND_ROWS();" );
		return $sql_result;
	}
	/** Author: ABU TAHER, Logic-coder IT
	 * get_interest_group_details
	 * Param $per_page = 5, $page_number = 1 ,$interest_group_id, $product_interest_id="", $group_details= ''
	 * Return Group Details
	 */
	public function get_interest_group_details( $per_page = 5, $page_number = 1 ,$interest_group_id , $product_interest_id='' , $group_details ){
		global $wpdb,  $current_date, $sql_posts_total, $search, $user_action;

		$where_query = '';

		/*if( $action== "move-out" && $product_interest_id ){
				$wp_interest_data['interest_group_id'] = 0;
				$wpdb->query(" UPDATE wp_product_interest SET interest_group_id =0 WHERE product_interest_id IN($product_interest_id) " );
		}
		if( $action== "add-to-group" && $product_interest_id ){
				$wp_interest_data['interest_group_id'] = $interest_group_id;
				$wpdb->query(	" UPDATE wp_product_interest SET interest_group_id ='".$interest_group_id."' WHERE product_interest_id IN($product_interest_id) " );
		}*/

		$sql_interest_group = " SELECT SQL_CALC_FOUND_ROWS * FROM {$wpdb->prefix}product_interest, {$wpdb->prefix}interest_group , {$wpdb->prefix}posts WHERE ( {$wpdb->prefix}product_interest.interest_group_id={$wpdb->prefix}interest_group.group_id AND {$wpdb->prefix}interest_group.group_id='".$interest_group_id."' ) AND ( {$wpdb->prefix}interest_group.product_id= {$wpdb->prefix}posts.ID AND {$wpdb->prefix}posts.post_status='publish') ";

		if( $search && empty( $group_details ) ){
			$sql_interest_group = " SELECT SQL_CALC_FOUND_ROWS * FROM {$wpdb->prefix}product_interest, {$wpdb->prefix}interest_group , {$wpdb->prefix}posts , {$wpdb->prefix}users WHERE ( {$wpdb->prefix}product_interest.interest_group_id={$wpdb->prefix}interest_group.group_id AND {$wpdb->prefix}interest_group.group_id='".$interest_group_id."' ) AND ( {$wpdb->prefix}interest_group.product_id= {$wpdb->prefix}posts.ID AND {$wpdb->prefix}posts.post_status='publish') ";

			$where_query  .= " AND ( ( {$wpdb->prefix}users.user_login LIKE '%" . esc_sql( str_replace( '*', '', $search ) ). "%'
				OR {$wpdb->prefix}users.user_nicename LIKE '%" . esc_sql( str_replace( '*', '', $search ) ). "%' OR {$wpdb->prefix}users.user_email LIKE '%" . esc_sql( str_replace( '*', '', $search ) ). "%' OR {$wpdb->prefix}users.display_name LIKE '%" . esc_sql( str_replace( '*', '', $search ) ). "%' ) AND (  {$wpdb->prefix}product_interest.user_id= {$wpdb->prefix}users.ID )  ) ";

		}

		if( $user_action === 'view-group-details' || $user_action ==='add-more-interests' || $user_action ==='set-group-price' ){
			$sql_interest_group  .= " AND {$wpdb->prefix}product_interest.interest_confirmed= 0 AND {$wpdb->prefix}product_interest.interest_campaign_closed= 0 ";
		}elseif( $user_action === 'view-confirmed-group-details' || $user_action ==='add-more-interests-to-confirmed-group' ){
			$sql_interest_group  .= " AND {$wpdb->prefix}product_interest.interest_confirmed= 1 ";
		}elseif( $user_action === 'view-failed-group-details' ){
			$sql_interest_group  .= " AND {$wpdb->prefix}product_interest.interest_campaign_closed= 2 ";
		}

		if( !empty( $where_query ) ){
			$sql_interest_group  .= $where_query;
		}

		if ( ! empty( $_REQUEST['orderby'] ) && 'login' === $_REQUEST['orderby'] ) {
			$sql_interest_group .= " ORDER BY user_id";
			$sql_interest_group .= ! empty( $_REQUEST['order'] ) ? ' ' . esc_sql( $_REQUEST['order'] ) : ' ASC';
		}
		elseif ( ! empty( $_REQUEST['orderby'] ) ) {
			$sql_interest_group .= ' ORDER BY ' . esc_sql( $_REQUEST['orderby'] );
			$sql_interest_group .= ! empty( $_REQUEST['order'] ) ? ' ' . esc_sql( $_REQUEST['order'] ) : ' ASC';
		}else{
			$sql_interest_group .= " ORDER BY {$wpdb->prefix}product_interest.interest_end_date ASC";
		}

		$sql_interest_group .= " LIMIT $per_page";
		$sql_interest_group .= ' OFFSET ' . ( $page_number - 1 ) * $per_page;

		$sql_interest_group_result = $wpdb->get_results( $sql_interest_group , 'ARRAY_A' );
		$sql_posts_total = $wpdb->get_var( "SELECT FOUND_ROWS();" );
		///echo $sql_interest_group;
		return $sql_interest_group_result;
	}

	/** Author: ABU TAHER, Logic-coder IT
	 * 	Detect when a bulk action is being triggered...
	*/
		public function process_bulk_action() {

				if ( 'remove-interest' === $this->current_action() ) {

				// In our file that handles the request, verify the nonce.
				$nonce = esc_attr( $_REQUEST['_wpnonce'] );

				$url_param = array(
						'product_id' => $_REQUEST['product_id'],
						'product_interest_id' => false,
						'action' => false,
						'user_action' => $_REQUEST['user_action'],
				);

				if ( ! wp_verify_nonce( $nonce, 'remove-interest' ) ) {
					die( 'Go get a life script kiddies' );
				}
				else {
					self::remove_interest( absint( $_REQUEST['product_interest_id'] ) );
					$_SESSION['interest_deleted'] = 1;
					wp_safe_redirect(  add_query_arg( $url_param )  );
					exit;
				}

			}
			//print_r( $_POST ); exit;
			// If the delete bulk action is triggered
			if ( ( isset( $_POST['action'] ) && $_POST['action'] === 'bulk-remove-interest' )
				 || ( isset( $_POST['action2'] ) && $_POST['action2'] === 'bulk-remove-interest' )
			) {

				$delete_ids = esc_sql( $_POST['product_interest_ids'] );

				// loop over the array of record IDs and delete them
				foreach ( $delete_ids as $id ) {
					self::remove_interest( $id );

				}
				$_SESSION['interest_deleted'] = sizeof( $delete_ids );
				//$url_param = array( );
				wp_safe_redirect( add_query_arg( ) );
				exit;
			}

			if ( 'add-interest-to-group' === $this->current_action() ) {

				$url_param = array(
					'product_interest_id' => false,
					'action' => false,

				);
				$add_success = self::add_interest_to_group( $_REQUEST['product_interest_id'] );
				if( $add_success ){
					$_SESSION['interest_added_to_group'] = 1;
				}
				wp_safe_redirect(  add_query_arg( $url_param )  );
				exit;
			}
			if ( ( isset( $_POST['action'] ) && $_POST['action'] === 'bulk-add-interest-to-group' )
				 || ( isset( $_POST['action2'] ) && $_POST['action2'] === 'bulk-add-interest-to-group' )
			) {
				$interest_ids = implode(',', esc_sql( $_POST['product_interest_ids'] ) ) ;
				// loop over the array of record IDs and delete them
				$add_success = self::add_interest_to_group( $interest_ids );
				if( $add_success ){
					$_SESSION['interest_added_to_group'] = sizeof( $_POST['product_interest_ids'] );
				}

				//$url_param = array( );
				wp_safe_redirect( add_query_arg( ) );
				exit;
			}

			if ( 'remove-group-price' === $this->current_action() ) {

				// In our file that handles the request, verify the nonce.
				$nonce = esc_attr( $_REQUEST['_wpnonce'] );

				if ( ! wp_verify_nonce( $nonce, 'remove-group-price' ) ) {
					die( 'Go get a life script kiddies' );
				}
				else {
					self::remove_group_price( absint( $_REQUEST['group_id'] ) , absint( $_REQUEST['group_price_id'] ) );
					$_SESSION['group_price_deleted'] = 1;
					$url_param = array(
						'action' => false,
					);
					wp_safe_redirect(  add_query_arg( $url_param )  );
					exit;
				}

			}
			//print_r( $_POST ); exit;
			// If the delete bulk action is triggered
			if ( ( isset( $_POST['action'] ) && $_POST['action'] === 'bulk-remove-group-price' )
				 || ( isset( $_POST['action2'] ) && $_POST['action2'] === 'bulk-remove-group-price' )
			) {
				$delete_ids = esc_sql( $_POST['group_price_ids'] );
				// loop over the array of record IDs and delete them
				foreach ( $delete_ids as $id ) {
					self::remove_group_price( absint($_REQUEST['group_id'] ) , $id );

				}
				$_SESSION['group_price_deleted'] = sizeof( $delete_ids );
				//$url_param = array(	);
				wp_safe_redirect( add_query_arg( ) );
				exit;
			}

			if ( 'interest-move-out' === $this->current_action() ) {
				// In our file that handles the request, verify the nonce.
				$nonce = esc_attr( $_REQUEST['_wpnonce'] );

				if ( ! wp_verify_nonce( $nonce, 'interest-move-out' ) ) {
					die( 'Go get a life script kiddies' );
				}
				else {
					self::interest_move_out( absint( $_REQUEST['product_interest_id'] ) );
					$_SESSION['interest_moved_out'] = 1;
					$url_param = array(
						'product_interest_id' => false,
						'action' => false,
					);
					wp_safe_redirect(  add_query_arg( $url_param )  );
					exit;
				}
			}
			// If the delete bulk action is triggered
			if ( ( isset( $_POST['action'] ) && $_POST['action'] === 'bulk-interest-move-out' )
				 || ( isset( $_POST['action2'] ) && $_POST['action2'] === 'bulk-interest-move-out' )
			) {
				$delete_ids = esc_sql( $_POST['product_interest_ids'] );

				// loop over the array of record IDs and delete them
				foreach ( $delete_ids as $id ) {
					self::interest_move_out( absint(  $id ) );

				}
				$_SESSION['interest_moved_out'] = sizeof( $delete_ids );
				//$url_param = array(	);
				wp_safe_redirect( add_query_arg( ) );
				exit;
			}

		}
	
		/** Author: ABU TAHER, Logic-coder IT
	 	* send_email_to_interest_group
	 	* Param $email_data, $group_details
		* return Success/Failure Message
		*/
	public function send_email_to_interest_group( $email_data, $group_details ){

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

				$group_price_list = $this ->get_group_price_by_id( $group_details[0]['group_id'] , $group_price_id='' );

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
					$subject="!NMID: ".$email_data["email_subject"]." CaseNo(".$group_details[0]['group_id'] ."_".$individual_data['product_interest_id'] .")\n\n";
					ob_start();
					include(get_stylesheet_directory_uri()."/email_header.php");
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
						<p>The Unit Price For Your Interest Is:" ."&nbsp;"<?php echo get_currency().":".$individual_data['interest_unit_price'];?></p><br/>
					<?php } ?>
					<p>Your Interest Details:</p><br/>
					<p><b>Product Name: </b>
					<a href="<?php echo get_site_url(); ?>/my-interest-list/?action=edit&product_interest_id=<?php echo $individual_data['product_interest_id'];?> &product_name=<?php echo $individual_data['post_name'];?> " ><?php echo $individual_data['product_name'];?> </a></p><br/>
					<p><b>Qty: </b><?php echo $individual_data['interest_qty'];?></p><br/>
					<p><b>Interest Start Date: </b><?php echo $interest_start_date;?></p><br/>
					<p><b>Interest End Date: </b><?php echo $interest_end_date;?></p><br/>
					<?php
					if( $email_data['confirmation_within'] ){ ?>
						<p>You Have&nbsp;<?php echo $email_data['confirmation_within'];?>Hours to confirm that you are still want to purchase this product for the above Details</p><br/>
					<?php } ?>
					<p>To confirm Please click on Yes:
						<a href="<?php echo get_site_url();?>/my-lists/?action=interest_confirmed&product_interest_id=<?php echo $individual_data['product_interest_id'];?>" >Yes</a>
						<a href="<?php echo get_site_url();?>/my-lists/?action=interest_notconfirmed&product_interest_id=<?php echo  $individual_data['product_interest_id'];?>" >No</a>
					</p><br/>
					<?php
					include(get_stylesheet_directory_uri()."/email_footer.php");
					$message = ob_get_contents();
					ob_end_clean();
					echo $message; exit;
					/////////////////////// End: Email Template ///////////////////////
					$email_to = $user_info->user_email;
					$format_array = array('%s', '%d', '%d', '%s', '%s',  '%s',  '%d',  '%s');
					if( wp_mail($email_to, $subject, $message )){
						//if( wp_mail( $email_to, $subject, $message, $header, '' ) )	{
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
						$succes_case_insert = $this ->insert_interest_case( $case_data , $format_array );
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
	public function send_email_to_interest_confirmed( $email_data, $interest_confirmed_details, $deal_selection ){
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
			$count_interest_confirmed = $this ->count_interest_confirmed( $interest_confirmed_details[0]['product_id'], $interest_confirmed_details[0]['group_id'] );
			//echo $count_interest_confirmed[0]->total_qty; exit;
			//////////////////////////////////////////////////////
			$product_meta_values = get_post_meta( $interest_confirmed_details[0]['product_id'], '', '' );
			$minimum_target_sells = $product_meta_values['minimum_target_sells'][0];
			if( $count_interest_confirmed[0]['total_qty'] < $minimum_target_sells ){
				$group_price_list_matched = $this ->get_minimum_price_list( $interest_confirmed_details[0]['group_id'] );
			}else{
				$group_price_list_matched = $this ->get_group_price_list_matched( $interest_confirmed_details[0]['group_id'], $count_interest_confirmed[0]['total_qty'] );
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
				//////////////////////////////////////////////
				$subject="!NMID: ".$email_data["email_subject"]." CaseNo(".$interest_confirmed_details[0]['group_id'] ."_".$individual_data['product_interest_id'] .")\n\n";
				$message  = "<html><body>"."\n";
				$message .="<table cellpadding='0' cellspacing='0' bgcolor=#319d00 width='100%' style='margin:0 auto'><tr style='font-family: Verdana,Arial,Helvetica,sans-serif; font-size: 11px; color: rgb(255,255,255); line-height: 140%;'><td width='23'></td><td><span>!NMID: A Business Aggregator</span></td></tr></table>"."\n\n";
				$message .="<p>Dear Customer &nbsp;".$dear_text.",</p>"."\n";
				$message .="<p>".$email_data["email_message_to_interest_grp"]."</p>"."\n";
				if( $deal_selection ==='want_to_deal' ){
					if( $individual_data['same_price_to_all'] || !intval( $individual_data['interest_unit_price'] ) ){
						//$same_price_to_all = 1;
						$message .="<p>A Price List is following for your interest:</p>"."\n";
						$message .="<table cellpadding='5' cellspacing='2' bgcolor=#ffffff width='100%' style='margin:0 auto'><tr style='font-family: Verdana,Arial,Helvetica,sans-serif; font-size: 11px; color: 333333; line-height: 140%;'>
						<td><span style='font-weight:bold;'>No Of Sells</span></td>
						<td><span style='font-weight:bold;'>Unit Price</span></td>
						<td><span style='font-weight:bold;'>Shipping Price</span></td>
						</tr>"."\n\n";
						$message .= $group_price_list_text . "</table>"."\n\n";
						}else{
							$message .="<p>The Unit Price For Your Interest Is:".get_currency()." : ".$individual_data['interest_unit_price']."</p>\n";
						}
					}
				$message .="<p>Your Interest Details:</p>"."\n";
				$message .="<p><b>Product Name: </b><a href=".get_site_url()."/my-interest-list/?action=edit&product_interest_id=".$individual_data['product_interest_id'] ."&product_name=".$individual_data['post_name'] ." >".$individual_data['product_name'] ."</a></p>\n";
				$message .="<p><b>Qty: </b>".$individual_data['interest_qty'] ."</p>\n";
				$message .="<p><b>Interest Start Date: </b>".$interest_start_date."</p>\n";
				$message .="<p><b>Interest End Date: </b>".$interest_end_date."</p>\n";
				if( $deal_selection=="want_to_deal"){
					if( $email_data['payment_within'] ){
						$message .="<p>You Have&nbsp;".$email_data['payment_within']."Hours For Payment to confirm that you are still want to purchase this product for the above Details</p>\n";
					}
					$message .="<p>For Payment Please click on This Link: <a href=".get_site_url()."/my-interest-list/?action=interest_confirmed&product_interest_id=".$individual_data['product_interest_id'] ." >Yes</a>
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
				//$header .= $message."\r\n\r\n";
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
				//if( mail( $email_to , $subject,"",$header) )	{
				if( wp_mail( $email_to, $subject, $message, $header, '' ) )	{
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

	/** Author: ABU TAHER, Logic-coder IT
	 * get_minimum_price_list
	 * Param $group_id
	 * Return Minimum Group Price List
	 */
	public function get_minimum_price_list( $group_id ){
		global $wpdb;
		$sql_result = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}interest_group_price WHERE  group_id='".$group_id."' AND no_of_sells= ( SELECT min(no_of_sells) FROM {$wpdb->prefix}interest_group_price WHERE group_id='".$group_id."')", 'ARRAY_A' );
		return $sql_result;
	}

	/** Author: ABU TAHER, Logic-coder IT
	 * get_group_price_list_matched
	 * Param $group_price_id="", $group_id, $action=""
	 * Return Group Price List
	 */
	public function get_group_price_list_matched( $group_id, $total_qty ){
		global $wpdb;
		//echo "SELECT * FROM {$wpdb->prefix}interest_group_price WHERE  group_id='".$group_id."' AND no_of_sells= ( SELECT max(no_of_sells) FROM {$wpdb->prefix}interest_group_price WHERE group_price_id IN (SELECT group_price_id FROM {$wpdb->prefix}interest_group_price WHERE $total_qty >= no_of_sells AND group_id='".$group_id."'";
		$sql_result = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}interest_group_price WHERE  group_id='".$group_id."' AND no_of_sells= ( SELECT max(no_of_sells) FROM {$wpdb->prefix}interest_group_price WHERE group_price_id IN (SELECT group_price_id FROM {$wpdb->prefix}interest_group_price WHERE $total_qty >= no_of_sells AND group_id='".$group_id."') ) ", 'ARRAY_A' );
		return $sql_result;
	}

	/** Author: ABU TAHER, Logic-coder IT
	 * user_product_interest_details
	 * Param $product_interest_id
	 * Return Product Interest Details
	 */
	public function user_product_interest_details( $product_interest_id ){
		global $wpdb;
		$sql_result = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}product_interest
		JOIN {$wpdb->prefix}posts ON {$wpdb->prefix}product_interest.product_id =
		{$wpdb->prefix}posts.ID WHERE {$wpdb->prefix}product_interest.product_interest_id = '".$product_interest_id."'", 'ARRAY_A' );
		return $sql_result;
	}
	/** Author: ABU TAHER, Logic-coder IT
	 * get_user_product_interest_meta
	 * Param $product_interest_id, $meta_type : product_meta= Meta for product,  interest_form_meta=Meta for interest, default empty
	 * Return interest Lists
	 */
	public function get_user_product_interest_meta( $product_interest_id, $meta_type='' ){
			global $wpdb, $where_query;
			$where_query ='';
			$sql_product_interest_meta = "SELECT * FROM {$wpdb->prefix}product_interest_meta
			JOIN {$wpdb->prefix}product_interest ON {$wpdb->prefix}product_interest_meta.product_interest_id = {$wpdb->prefix}product_interest.product_interest_id";

			if( !empty( $product_interest_id ) ){
				$where_query .= " WHERE {$wpdb->prefix}product_interest_meta.product_interest_id = '".$product_interest_id."'";
			}

			if( !empty( $meta_type ) ){
				if( !empty( $where_query ) ){
					$where_query  .= " AND {$wpdb->prefix}product_interest_meta.meta_type='".$meta_type."' ";
				}else{
					$where_query  .= " {$wpdb->prefix}product_interest_meta.meta_type='".$meta_type."' ";
				}
			}

			if( !empty( $where_query ) ){
				$sql_product_interest_meta  .= $where_query;
			}

			$sql_result = $wpdb->get_results( $sql_product_interest_meta , 'ARRAY_A' );
			return $sql_result;
	}

	/** Author: ABU TAHER, Logic-coder IT
	 * wp_product_interest_meta_insert
	 * Param $product_interest_insert_id, $wp_product_interest_data, $interest_attributes
	 */
	public function wp_product_interest_meta_insert( $product_interest_insert_id, $wp_product_interest_data, $interest_attributes ){
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
				$wpdb->insert( "{$wpdb->prefix}product_interest_meta", $wp_product_interest_meta_data , $format_array );
			}
		}
		return $wpdb->insert_id;
	}
	/** Author: ABU TAHER, Logic-coder IT
	 * wp_product_interest_meta_update
	 * Param $product_interest_id, $wp_product_interest_data, $product_attributes
	 */
	function wp_product_interest_meta_update( $product_interest_id, $wp_product_interest_data, $product_attributes ){
		global $wpdb;
		$where = array( 'product_interest_id' => $product_interest_id );
		$where_format = array();
		$success = $wpdb->delete( "{$wpdb->prefix}product_interest_meta", $where, $where_format = null );
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
					$wpdb->insert( "{$wpdb->prefix}product_interest_meta", $wp_product_interest_meta_data , $format_array );
				}
			}
			return $wpdb->insert_id;
		//}
	}
	/** Author: ABU TAHER, Logic-coder IT
	 * insert_interest_case
	 * Param $case_data, $format_array
	* return Success/Failure Message
	 */
	public function insert_interest_case( $case_data, $format_array ){
			global $wpdb;
			$wpdb->insert( "{$wpdb->prefix}interest_group_case", $case_data , $format_array );
			return $wpdb->case_id;
		}
	}

endif;
