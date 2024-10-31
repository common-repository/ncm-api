<?php

class Add_comment {
    public function __construct($order_id){
       //URL of API
	global $wpdb;
	$table_name = $wpdb->prefix.'comments';
	 
	$endpoint = "https://portal.nepalcanmove.com/api/v1/comment";

	$vendor_id = get_post_meta($order_id, 'vendor_id', true);
	$query = $wpdb->get_results( "SELECT * FROM $table_name WHERE comment_post_ID = $order_id ORDER BY comment_date DESC LIMIT 1");
	$my_id = $query[0]->comment_ID;
	$comment_id_7 = get_comment( $my_id, ARRAY_A );
	$name = $comment_id_7['comment_content'];
	$author = $comment_id_7['comment_author'];
		//check if the order is already created or not
		if(!$vendor_id){
			//setup data to be send by API Endpoint
			
		}
        else{
			if($author != 'WooCommerce')
			{
            	$data = array(
					'orderid' => $vendor_id,
                	'comments' => $name
				);

				// send API request via cURL
				$ch = curl_init();

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

				if ( is_wp_error( $response ) || wp_remote_retrieve_response_code( $response ) != 200 ) 
				{
					var_dump( $response );
				} 
				else {
					$order_response = json_decode( wp_remote_retrieve_body( $response ) );
				}
			}
        }
    }

}

	

