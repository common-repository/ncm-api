<div class="container-fluid mt-3">
	<div class="row">
        <div class="col-md-10">
            <h2 class="pl-4">NCM API - Dashboard</h2>
        </div>
        <div class="col-md-2">
		<a href=https://portal.nepalcanmove.com/ target="_blank"><img src="<?PHP echo NCM_API_PLUGIN_URI . '/assets/rect-icon.png'?>" style="height:50px; width:200px;"/></a>
        </div>
    </div>
	<hr style="border:1px solid red;">
    <div class="container-sm">
        <div class="row mb-3">
	        <form method="POST">
		        <input type="hidden" name="updated" value="true" />
		        <?php wp_nonce_field( 'api_update', 'api_token_form' ); ?>
		        <table class="form-table">
			        <tbody>
				        <tr>
					        <th>
                                <label for="ncm_token">API Token Key : </label>
                            </th>
					        <td>
                                <input type="text" class="form-control" name="ncm_token" id="ncm_token" value="<?php echo esc_attr( get_option('api_token') ) ?>">
                            </td>
				        </tr>
			        </tbody>
		        </table>
		        <p class="submit float-end">
			        <input type="submit" name="submit" id="submit" class="button button-primary" value="Save Token">
		        </p>
	        </form>
        </div>
        <div class="row mb-3">
            <h2>NCM Portal Announcement</h2>
		    <?php 
				if(isset($data['detail']) == "Invalid token."){
			?>
				<div class="shadow p-3 mb-2 bg-body rounded">
			    	<center><h4> Invalid API Token Key </h4></center>
		    	</div>	
			
			<?php
				}

				else{
			    	foreach($data as $key){
		    ?>
            	<div class="shadow p-3 mb-2 bg-body rounded">
			    	<h4><?php echo esc_html( $key->title ); ?></h4>
                	<hr style="border:1px solid red;">
		    		<p><?php echo html_entity_decode( $key->content ); ?></p>
		    	</div>
	        <?php }} ?>
        </div>
    </div>
</div>