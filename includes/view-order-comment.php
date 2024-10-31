<?php
/**
 * Manage menu items and pages.
 */



add_action( 'woocommerce_admin_order_data_after_shipping_address', 'admin_custom_row_after_order_addresses', 10, 1 );
function admin_custom_row_after_order_addresses( $order ){
    
    $vendor_id = get_post_meta($order->get_id(), 'vendor_id', true);

    if($vendor_id) :

    $comment_url = 'https://portal.nepalcanmove.com/api/v1/order/comment?id='.$vendor_id;
        //Api Authorization Key
	$token = get_option('api_token', true);
	$authorization = "Token ".$token;
    
    $comment_arguments = array(
        'method' => 'GET',
        'headers'   => array(
			'Content-Type' => 'application/json; charset=utf-8', 
			'Authorization' => $authorization,
		),
    );

    $comment_response = wp_remote_get( $comment_url, $comment_arguments );

    $data = (array) json_decode( wp_remote_retrieve_body( $comment_response ) );

        
    $order_url = 'https://portal.nepalcanmove.com/api/v1/order?id='.$vendor_id;
        //Api Authorization Key
	
    $order_arguments = array(
        'method' => 'GET',
        'headers'   => array(
		    'Content-Type' => 'application/json; charset=utf-8', 
		    'Authorization' => $authorization
	    ),
    );

    $order_response = wp_remote_get( $order_url, $order_arguments );
    $rtv_data = (array) json_decode( wp_remote_retrieve_body( $order_response ) );

    $rtv = $rtv_data['vendor_return'];
    $delivery_status = $rtv_data['last_delivery_status'];
    if(isset($data['detail'])):
        $total_comment = 0;
        
        $insert_data = array(
            'total_comment' => $total_comment,
            'rtv' => $rtv,
            'delivery_status' => $delivery_status
        );
        
        update_post_meta( $order->get_id(), 'insert_data', $insert_data );

        ?>
        </div></div>
        <div class="clear"></div>
        <!-- new custom section row -->
        <div class="order_data_column_container">
            <div class="order_data_column_wide">
                <h3><?php echo esc_html($data['detail']) ?></h3>
    <?php
    else :
    if($data) :
        $total_comment = count($data);

        $insert_data = array(
            'total_comment' => $total_comment,
            'rtv' => $rtv,
            'delivery_status' => $delivery_status
        );
        
        update_post_meta( $order->get_id(), 'insert_data', $insert_data );

    ?>
        </div></div>
        <div class="clear"></div>
        <!-- new custom section row -->
        <div class="order_data_column_container">
            <div class="order_data_column_wide">
                <h3><?php _e("Comment Form NCM Vendor Portal"); ?></h3>
                <!-- custom row paragraph -->
                <hr style ="border:1px solid red">
    <?php
    
		foreach($data as $key){
	?>
    <ul>
        <?php
            $newDate = DateTime::createfromFormat('Y-m-d\TH:i:s.uP',$key->added_time);    
        ?>
        <li><strong>Comment: <?php echo esc_html($key->comments) ?> <strong></li>
        <li><strong>Added By: <?php echo esc_html($key->addedBy) ?> <strong></li>
        <li><strong>Date Created: <?php echo esc_html($newDate->format('Y-m-d H:i')) ?> <strong></li>
        <hr>
        </ul>
    
    <?php 
  
    }
    endif ;
    endif ; 
    endif ;
}