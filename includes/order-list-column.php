<?php

/**
 * Adds 'IS NCM' column header to 'Orders' page immediately after 'Total' column.
 *
 * @param string[] $columns
 * @return string[] $new_columns
 */
function is_ncm_column_header( $columns ) {

    $new_columns = array();

    foreach ( $columns as $column_name => $column_info ) {

        $new_columns[ $column_name ] = $column_info;

        if ( 'order_total' === $column_name ) {
            $new_columns['is_ncm'] = __( 'Sent to NCM?', 'my-textdomain' );
        }
    }

    return $new_columns;
}

add_filter( 'manage_edit-shop_order_columns', 'is_ncm_column_header', 20 );

if ( ! function_exists( 'sv_helper_get_order_meta' ) ) :

    /**
     * Helper function to get meta for an order.
     *
     * @param \WC_Order $order the order object
     * @param string $key the meta key
     * @param bool $single whether to get the meta as a single item. Defaults to `true`
     * @param string $context if 'view' then the value will be filtered
     * @return mixed the order property
     */
    function sv_helper_get_order_meta( $order, $key = '', $single = true, $context = 'edit' ) {

        // WooCommerce > 3.0
        if ( defined( 'WC_VERSION' ) && WC_VERSION && version_compare( WC_VERSION, '3.0', '>=' ) ) {

            $value = $order->get_meta( $key, $single, $context );

        } else {

            // have the $order->get_id() check here just in case the WC_VERSION isn't defined correctly
            $order_id = is_callable( array( $order, 'get_id' ) ) ? $order->get_id() : $order->id;
            $value    = get_post_meta( $order_id, $key, $single );
        }

        return $value;
    }

endif;

/**
 * Adds 'Profit' column content to 'Orders' page immediately after 'Total' column.
 *
 * @param string[] $column name of column being displayed
 */
function is_ncm_column_content( $column ) {
    global $post;

    if ( 'is_ncm' === $column ) {

        $order       = wc_get_order( $post->ID );
        $branchname  = get_post_meta( $order->get_id(), 'branchname', true );
	    $is_ncm      = get_post_meta( $order->get_id(), 'is_ncm', true );
    ?>
        
    <?php if($branchname) :?>
		<mark class="order-status status-processing tips"><span>Yes</span></mark>
	<?php
		endif ;
	?>

    <?php if(!$branchname) :?>
		<mark class="order-status status-failed tips"><span>No</span></mark>
	<?php
		endif ;
	?>
	
   <?php }
}
add_action( 'manage_shop_order_posts_custom_column', 'is_ncm_column_content' );
/**
 * Adjusts the styles for the new 'IS NCM' column.
 */
function is_ncm_column_style() {

    $css = '.widefat .column-order_date, .widefat .column-is_ncm { text-align: center}';
    wp_add_inline_style( 'woocommerce_admin_styles', $css );
}
add_action( 'admin_print_styles', 'is_ncm_column_style' );