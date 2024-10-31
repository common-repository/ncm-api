<?php
/*
*Upload the orderto the NCM Vendor Portal 
*/
require_once(plugin_dir_path(__FILE__).'../includes/add-comment.php');


add_action( 'woocommerce_admin_order_data_after_order_details', 'ncm_add_comment', 10, 1 );
function ncm_add_comment( $order ){
	$comment = '';
	?>

	<br class="clear" />
	<h4>Add Comment to Portal <a href="#" class="edit_address">Add</a></h4>

	<div class="edit_address"><?php
		woocommerce_wp_textarea_input( array( 
			'id' => 'comment',
			'label' => 'Comment', 
			'wrapper_class' => 'form-field-wide',
			'class' => 'date-picker',
			'style' => 'width:100%',
			'value' => $comment,
			'description' => 'Add you Comment to NCM Vendor Portal.'
		));
		?>
	</div>
	<?php
}

add_action( 'woocommerce_process_shop_order_meta', 'ncm_save_portal_comment' );

function ncm_save_portal_comment( $ord_id ){
	global $wpdb;
	$date = date('Y-m-d H:i:s');
	// $compacted = compact( 233, 'NCM', '', '', '',$date, $date, 'test', 0, 1, '', 'comment', 0, 0 );
	$current_user = wp_get_current_user();
	
        $data = array(
            'comment_post_ID'      => $ord_id,
            'comment_content'      => wc_clean($_POST['comment']),
            'comment_parent'       => 0,
            'user_id'              => $current_user->ID,
            'comment_author'       => $current_user->user_login,
            'comment_author_email' => $current_user->user_email,
            'comment_author_url'   => $current_user->user_url,
			'comment_date'		   => $date,
			'comment_date_gmt'     => $date
        );
 
		$wpdb->insert( $wpdb->comments, $data );
    
}

function order_create($order_id){
	if(isset($order_id)){
		//get order object and order details
		$order = wc_get_order($order_id);
		$order_data = $order->get_data(); 
	
		if(!($order_data['shipping']['phone'])){
			$billing_phone = $order_data['billing']['phone'];
		}
		else{
			$billing_phone = $order_data['shipping']['phone'];
		}
		if(!($order_data['shipping']['first_name']) || !($order_data['shipping']['last_name'])){
			$shipping_firstname = $order_data['billing']['first_name'];
			$shipping_lastname = $order_data['billing']['last_name'];
		}
		else{
			$shipping_firstname = $order_data['shipping']['first_name'];
			$shipping_lastname = $order_data['shipping']['last_name'];
		}
		if(!($order_data['shipping']['address_1'])){
			$shipping_address = $order_data['billing']['address_1'];
		}
		else{
			$shipping_address = $order_data['shipping']['address_1'];
		}
		
		$total = $order_data['total'];
		
		$items = $order->get_items();     
		$count = count($items);
		$category_name = '';

		if($count > 1){
			foreach ( $items as $item ) {
				
					// $product = $item->get_product();
					// $categorieID = $product->category_ids[0];
					// $categorie_title = get_the_category_by_ID($categorieID);
					// $category_name = $categorie_title.', '.$category_name;
				$category_name = '';
			}
		}
		else{
			foreach ( $items as $item ) {
					$product = $item->get_product();
					$categorieID = $product->category_ids[0];
					$category_name = get_the_category_by_ID($categorieID);
				
			}
		}
		
	
		//URL of API
		$endpoint = "https://portal.nepalcanmove.com/api/v1/order/create";
	
		$branchname = get_post_meta( $order_data['id'], 'branchname', true );
		$is_ncm = get_post_meta( $order_data['id'], 'is_ncm', true );
		$vendor_id = get_post_meta($order_data['id'], 'vendor_id', true);
		
		if(!$is_ncm && !$branchname){
			//Donot call the Api
		}
		else{
			//check if the order_data is already created or not
			if(!$vendor_id){
				//setup data to be send by API Endpoint
				$total_comment = 0;
        
        		$insert_data = array(
            		'total_comment' => $total_comment,
            		'rtv' => 'False',
					'delivery_status' => 'Pickup Order Created'
       			);
        
        		update_post_meta( $order->get_id(), 'insert_data', $insert_data );

				$data = array(
					"name" => $shipping_firstname.' '.$shipping_lastname ,
					"phone" => $billing_phone,
					"cod_charge" => $total,
					"address" => $shipping_address,
					"branch" => $branchname,
					"vref_id" => (string)$order_data['id'],
					"package" => $category_name,
					
				);

				//Api Authorization Key
				$token = get_option('api_token', true);
				$authorization = "Token ".$token;

				$args = array(
					'method'      => 'POST',
					'timeout'     => 45,
					'sslverify'   => false,
					'headers'     => array(
						'Authorization' => $authorization,
						'Content-Type'  => 'application/json',
					),
					'body'        => json_encode($data),
				);

				
				$response = wp_remote_post( $endpoint, $args );

				// var_dump($options);

				if ( is_wp_error( $response ) || wp_remote_retrieve_response_code( $response ) != 200 ) 
				{
					var_dump( $response );
				} 
				else {
					$order_response = json_decode( wp_remote_retrieve_body( $response ) );
					
					$vend_id = $order_response->orderid;

					update_post_meta( $order_data['id'], 'vendor_id', $vend_id);
				}
			}
			new Add_comment($order_data['id']);
			remove_action('woocommerce_update_order', 'order_create');
		}
	}

}

/* after an order has been processed, we will use the  'woocommerce_thankyou' hook, to add our function, to send the data */
add_action( 'woocommerce_update_order', 'order_create');
