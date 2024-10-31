<?php 
    $query = new WC_Order_Query(
        array(
            'status'=> array('wc-processing', 'wc-refund', 'wc-on-hold', 'wc-pending'),
            'meta_key'=>"is_ncm",
            "meta_compare"=>"NOT EXISTS"
        )
    );
?>


<div class="container-fluid mt-3">
    <div class="row">
        <div class="col-md-10">
            <h2 class="pl-4">NCM API - Non NCM Orders</h2>
        </div>
        <div class="col-md-2">
        <a href=https://portal.nepalcanmove.com/ target="_blank"><img src="<?PHP echo NCM_API_PLUGIN_URI . '/assets/rect-icon.png'?>" style="height:50px; width:200px;"/></a>
        </div>
    </div>
	<hr style="border:1px solid red;">
    <div class="container-sm">    
    <table id="ncm-api-table" class="table table-striped" style="width:100%">
            <thead>
                <tr>
                    <th>Order</th>
                    <th>Receiver</th>
                    <th>Status</th>
                    <th>Total</th>
                    <th>Date</th>
                    <th>View</th>
                    <th>Sent to NCM</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $orders = $query->get_orders();
                    foreach( $orders as $order_id ) {
                ?>
                <tr>
                    <td><?php echo esc_html($order_id->get_id()); ?></td>
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
                    <td style="text-align: right"><?php echo "RS. ".esc_html($order_id->get_total()); ?></td>
                    <td><?php echo substr($order_id->get_date_created(),0,10); ?></td>
                    <td><a href="<?php echo esc_url(get_admin_url().'post.php?post='.$order_id->get_id().'&action=edit') ?>"> <i class="fa fa-eye" style="color:black"></i></a></td>
                    <td><a type="button" data-bs-toggle="modal" data-bs-target="#exampleModal"><i class="fa fa-share" style="color:black"></i></a></td>
                </tr>
                <?php
                    }
                ?>
            </tbody>
            <tfoot>
                <tr>
                    <th>Order</th>
                    <th>Receiver</th>
                    <th>Status</th>
                    <th>Total</th>
                    <th>Date</th>
                    <th>View</th>
                    <th>Sent to NCM</th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Please Select Branch</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <form method="POST">
		        <input type="hidden" name="updated" value="true" />
		        <?php
                    if(isset($_POST['branch_name'])){
                        $branch_name = wc_clean($_POST['branch_name']);

                        update_post_meta( $order_id->get_id(), 'branchname', $branch_name );
                        update_post_meta( $order_id->get_id(), 'is_ncm', 1 );
                        
                        order_create($order_id->get_id());
                        
                        header("Refresh:0");
                    }
                ?>
		        <table class="form-table">
			        <tbody>
				        <tr>
                            <?php
                                $url = 'https://portal.nepalcanmove.com/api/v1/branchlist';
    
                                $arguments = array(
                                    'method' => 'GET'
                                );
                            
                                $response = wp_remote_get( $url, $arguments );
                                
                                //$data = json_decode(wp_remote_retrive_body($reponse));
                            
                                $data = (array) json_decode( wp_remote_retrieve_body( $response ) );
                                
                                $arraydata = json_decode($data['data']);
                                
                               
                            ?>
					        <th style="width:30%">
                                <label for="ncm_token">Branch Name : </label>
                            </th>
					        <td>
                                <select class="form-select form-select-lg mb-3" aria-label=".form-select-lg example" name="branch_name">    
                                    <option selected>Please Select Branch</option>
                                    <?php 
                                        foreach( $arraydata as $data ) {
                                    ?>
                                        <option value=<?php echo esc_attr( $data[0] ); ?>><?php echo esc_attr( $data[0] ); ?></option>
                                    <?php
                                        }
                                    ?>
                                </select>
                            </td>
				        </tr>
			        </tbody>
		        </table>
		        <p class="submit float-end">
			        <input type="submit" name="submit" id="submit" class="btn btn-success" value="Submit">
		        </p>
	        </form>
      </div>
    </div>
  </div>
</div>