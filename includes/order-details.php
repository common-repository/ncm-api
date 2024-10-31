<?php
add_action( 'woocommerce_admin_order_data_after_order_details', 'misha_editable_order_meta_general' );

function misha_editable_order_meta_general( $order ){  
        
    $vendor_id = get_post_meta($order->get_id(), 'vendor_id', true); 
        
    if($vendor_id) :
        
        $url = 'https://portal.nepalcanmove.com/api/v1/order?id='.$vendor_id;
        //Api Authorization Key
	    $token = get_option('api_token', true);
	    $authorization = "Token ".$token;
    
        $arguments = array(
            'method' => 'GET',
            'headers'   => array(
			    'Content-Type' => 'application/json; charset=utf-8', 
			    'Authorization' => $authorization
		    ),
        );

        $response = wp_remote_get( $url, $arguments );

        $data = (array) json_decode( wp_remote_retrieve_body( $response ) );
		

        ?>
 
        
            <br class="clear" /><br><br>
            <h4>Order Detail in NCM Portal</h4>
            <hr style = "border: 1px solid red;">
            <table class="form-table">
				<tbody>
					<tr>
						<td style = "padding:7px 10px!important;"><strong>Order Id : </strong></td>
						<td style = "padding:7px 10px!important;"><?php echo esc_html( $vendor_id ) ?></td>
					</tr>
					<tr>
						<td style = "padding:7px 10px!important;"><strong>Delivery Charge : </strong></td>
						<td style = "padding:7px 10px!important;"><?php echo esc_html( $data['delivery_charge'] ) ?></td>
					</tr>
                    <tr>
						<td style = "padding:7px 10px!important;"><strong>Payment Status : </strong></td>
						<td style = "padding:7px 10px!important;"><?php echo esc_html( $data['payment_status'] ) ?></td>
					</tr>
                    <tr>
						<td style = "padding:7px 10px!important;"><strong>Last Delivery Status : </strong></td>
						<td style = "padding:7px 10px!important;"><?php echo esc_html( $data['last_delivery_status'] ) ?></td>
					</tr>
					<tr>
						<td style = "padding:7px 10px!important;"><strong>RTV : </strong></td>
						<td style = "padding:7px 10px!important;"><?php echo esc_html( $data['vendor_return'] ) ?></td>
					</tr>
				</tbody>
			</table>
            
            <?php
     endif ;
}

