<?php

add_action( 'woocommerce_admin_order_data_after_shipping_address', 'ncm_add_branch' );	 	
function ncm_add_branch( $order ){
    $url = 'https://portal.nepalcanmove.com/api/v1/branchlist';
    
    $arguments = array(
        'method' => 'GET'
    );

	$response = wp_remote_get( $url, $arguments );
    
    //$data = json_decode(wp_remote_retrive_body($reponse));

    $data = (array) json_decode( wp_remote_retrieve_body( $response ) );
	
	$arraydata = json_decode($data['data']);
	
	$branchlist[''] = __('Select a Branch', 'woocommerce');

	foreach($arraydata as $key){
		$branchlist[$key[0]] = $key[0];
		// var_dump($branchlist[$key[0]]);
	}

    $branchname = get_post_meta( $order->get_id(), 'branchname', true );
	$is_ncm = get_post_meta( $order->get_id(), 'is_ncm', true );
	
    ?>
	<?php if(!$branchname) :?>
	<br class="clear" />
	<h4>Upload Order To NCM Portal <a href="#" class="edit_address">Edit</a></h4>
    
	<div class="address">
		<p><strong>Send Via NCM?</strong><?php echo esc_html($is_ncm ? 'Yes' : 'No') ?></p>
		<?php
			// we show the rest fields in this column only if this order is marked as a gift
			if( $is_ncm ) :
		?>
		<p<?php if( !$branchname ) echo ' class="none_set"' ?>>
			<strong>Branch Name:</strong>
			<?php echo esc_html(( $branchname ) ? $branchname : 'No branch selected.') ?>
		</p>
		<?php
			endif ;
		?>
	</div>
	<?php
		endif ;
	?>
	<?php if($branchname) : 
	?>
		
	<br class="clear" />
	<h4>Order Placed in NCM Portal</h4>
    
	<div class="address">
		<p><strong>Send Via NCM : </strong>Yes</p>
		<p>
			<strong>Branch Name:</strong>
			<?php echo esc_html( $branchname ) ?>
		</p>
	</div>
	<?php
		endif ;
	?>
	
    <div class="edit_address"><?php
        woocommerce_wp_radio( array(
			'id' => 'is_ncm',
			'label' => 'Send Via NCM?',
			'value' => $is_ncm,
			'options' => array(
				'' => 'No',
				'1' => 'Yes'
			),
			'style' => 'width:16px', // required for checkboxes and radio buttons
			'wrapper_class' => 'form-field-wide' // always add this class
		) );
		
		woocommerce_wp_select( array( 
			'id' => 'branchname',
			'label' => 'Delivery Branch Name', 
			'wrapper_class' => 'form-field-wide misha-set-tip-style',
			'value' => $branchname,
			'desc_tip' => true,
			'options' => $branchlist
		) );
        ?></div><?php
		
}

add_action( 'woocommerce_process_shop_order_meta', 'ncm_save_branch_details' );

function ncm_save_branch_details( $ord_id ){
	update_post_meta( $ord_id, 'is_ncm', wc_clean( $_POST[ 'is_ncm' ] ) );
	update_post_meta( $ord_id, 'branchname', wc_clean( $_POST[ 'branchname' ] ) );
}




// Register Taxonomy test
