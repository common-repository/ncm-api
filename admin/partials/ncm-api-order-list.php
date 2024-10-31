<?php 
    $query = new WC_Order_Query(
        array(
            'limit'=>-1,
            'status'=> array('wc-processing','wc-completed','wc-cancelled', 'wc-refund', 'wc-failed', 'wc-on-hold', 'wc-pending'),
            'meta_key'=>"is_ncm",
            "meta_compare"=>"1"
        )
    );

    
?>
<div class="container-fluid mt-3">
    <div class="row">
        <div class="col-md-10">
            <h2 class="pl-4">NCM API - NCM Orders</h2>
        </div>
        <div class="col-md-2">
        <a href=https://portal.nepalcanmove.com/accounts/vendor/orders target="_blank"><img src="<?PHP echo NCM_API_PLUGIN_URI . '/assets/rect-icon.png'?>" style="height:50px; width:200px;"/></a>
        </div>
    </div>
	
	<hr style="border:1px solid red;">
    <div class="container-sm">
        <!-- <div class="row">
            <div class="col-md-10">
            </div>
            <div class="col-md-1">
            </div>
            <div class="col-md-1">
                
            </div>
        </div> -->
        <table id="ncm-api-table" class="table table-striped" style="width:100%">
            <thead>
                <tr>
                    <th>Order</th>
                    <th>RTV</th>
                    <th>Delivery Branch</th>
                    <th>Receiver</th>
                    <th>Status</th>
                    <th>Total</th>
                    <th>Total Comment</th>
                    <th>NCM Delivery Status</th>
                    <th>Date</th>
                    <th>View</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $orders = $query->get_orders();
                    foreach( $orders as $order_id ) {
                        $vendor_id = get_post_meta($order_id->get_id(), 'vendor_id', true);
                        
                        
                        $get_data = get_post_meta($order_id->get_id(), 'insert_data', true);
                ?>
                <tr>
                    <td><?php echo esc_html($order_id->get_id()); ?></td>
                    <td>
                        <?php 
                            if(isset($get_data['rtv']) && $get_data['rtv'] == 'True'){
                                ?> <i class="fa fa-check" style="color:red">
                            <?php }
                            else{
                                ?>-<?php
                            }
                        ?>
                    </td>
                    <td><?php echo esc_html(get_post_meta( $order_id->get_id(), 'branchname', true ))?></td>
                    <td style="text-align: left;">
                        <?php
                            if( !($order_id->get_shipping_first_name()) || !($order_id->get_shipping_last_name())){
                                echo esc_html($order_id->get_billing_first_name().' '.$order_id->get_billing_last_name());    
                            } 
                            else{

                                echo esc_html($order_id->get_shipping_first_name().' '.$order_id->get_shipping_last_name()); 
                            }
                        ?>
                    </td>
                    
                    <td>
                        <?php 
                            if($order_id->get_status() == 'processing'):
                        ?>
                        <span class="badge rounded-pill bg-primary">
                            Processing
                        </span>
                        <?php
                            endif;
                        ?>
                        <?php 
                            if($order_id->get_status() == 'cancelled'):
                        ?>
                        <span class="badge rounded-pill bg-danger">
                            Cancelled
                        </span>
                        <?php
                            endif;
                        ?>
                        <?php 
                            if($order_id->get_status() == 'on-hold'):
                        ?>
                        <span class="badge rounded-pill bg-info">
                            On Hold
                        </span>
                        <?php
                            endif;
                        ?>
                        <?php 
                            if($order_id->get_status() == 'completed'):
                        ?>
                        <span class="badge rounded-pill bg-success">
                            Completed
                        </span>
                        <?php
                            endif;
                        ?>
                        <?php 
                            if($order_id->get_status() == 'failed'):
                        ?>
                        <span class="badge rounded-pill bg-warning">
                            Failed
                        </span>
                        <?php
                            endif;
                        ?>
                        <?php 
                            if($order_id->get_status() == 'pending'):
                        ?>
                        <span class="badge rounded-pill bg-secondary">
                            Payment Pending 
                        </span>
                        <?php
                            endif;
                        ?>
                    </td>
                    <td style="text-align: right;"><?php echo esc_html("RS. ". $order_id->get_total()); ?></td>
                    <td>
                        <span class="badge rounded-pill bg-danger">
                            <?php $commentTotal = isset($get_data['total_comment']) ? $get_data['total_comment'] : 0; ?>
                            <?php echo esc_html($commentTotal) ?> 
                        </span>
                    </td>
                    <?php $deliveryStatus = isset($get_data['delivery_status']) ? $get_data['delivery_status'] : 'No Status found'; ?>
                    <td><?php echo esc_html($deliveryStatus) ?> </td>
                    <td><?php echo substr($order_id->get_date_created(),0,10); ?></td>
                    <td>
                        <a href="<?php echo esc_url(get_admin_url().'post.php?post='.$order_id->get_id().'&action=edit') ?>"> <i class="fa fa-eye" style="color:black"></i></a>
                        <a href="https://portal.nepalcanmove.com/accounts/vendor/order/".$vendor_id. target="_blank"> <img src="<?php echo NCM_API_PLUGIN_URI . 'assets/icon.png' ?>" style="height:20px; width:20px;"/></a>
                    </td>
                </tr>
                <?php
                    }
                ?>
            </tbody>
            <tfoot>
                <tr>
                    <th>Order</th>
                    <th>RTV</th>
                    <th>Delivery Branch</th>
                    <th>Receiver</th>
                    <th>Status</th>
                    <th>Total</th>
                    <th>Total Comment</th>
                    <th>NCM Delivery Status</th>
                    <th>Date</th>
                    <th>View</th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>