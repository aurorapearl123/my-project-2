<div class="subheader">
	<div class="d-flex align-items-center">
		<div class="title mr-auto">
			<h3><i class="icon left la <?php echo $current_module['icon'] ?>"></i> <?php echo $current_module['title'] ?></h3>
		</div>
		<div class="subheader-tools">
			<a href="<?php echo site_url('clothes_category/show') ?>" class="btn btn-primary btn-raised btn-sm pill"><i class="icon left ti-angle-left md"></i> Back to List</a>
		</div>
	</div>
</div>
<div class="content">
	<div class="row">
		<div class="col-12">
			<div class="card-box">
				<div class="card-head">
					<div class="head-caption">
						<div class="head-title">
							<h4 class="head-text">View <?php echo $current_module['module_label'] ?></h4>
						</div>
					</div>
					<div class="card-head-tools">
						<ul class="tools-list">
							<?php if ($roles['edit']) {?>
							<li>
								<a href="<?php echo site_url('clothes_category/edit/'.$this->encrypter->encode($rec->clothesCatID)) ?>" class="btn btn-outline-light bmd-btn-icon" data-toggle="tooltip" data-placement="bottom" data-original-title="Edit"><i class="la la-edit"></i></a>
							</li>
							<?php } ?>
							<?php if ($roles['delete'] && !$in_used) {?>
							<li>
								<button name="cmddelete" id="cmddelete" class="btn btn-outline-light bmd-btn-icon" data-toggle="tooltip" data-placement="bottom" data-original-title="Delete" onclick="deleteRecord('<?php echo $this->encrypter->encode($rec->clothesCatID); ?>');"><i class="la la-trash-o"></i></button>
							</li>
							<?php } ?>
							<?php if ($this->session->userdata('current_user')->isAdmin) {?>
							<li>
								<button type="button" id="recordlog" class="btn btn-outline-light bmd-btn-icon" data-toggle="tooltip" data-placement="bottom" data-original-title="Record Logs" onclick="popUp('<?php echo site_url('logs/record_log/clothes_category/clothesCatID/'.$this->encrypter->encode($rec->clothesCatID).'/Clothes Category') ?>', 1000, 500)"><i class="la la-server"></i></button>
							</li>
							<?php } ?>
						</ul>
					</div>
				</div>
				<div class="card-body">
					<div class="data-view">
						<table class="view-table">
							<tbody>

								<tr>
									<td class="data-title w-10">Category:</td>
									<td class="data-input w-20"><?php echo $rec->category; ?></td>
									<td class="d-xxl-none"></td>
								</tr>
								<tr>
									<td class="data-title w-10">Specific:</td>
									<td class=" w-20">
										<input type="checkbox" name="specific" value="specific" id="specific"> <br>
											
									</td>
									<td class="d-xxl-none"></td>
								</tr>
								<tr>
									<td class="data-title w-10">Service</td>
								</tr>
								<tr>	
									<td class="d-xxl-none"></td>								
									<td >	

										<?php foreach($service_types as $servc) {?>
										
											<input type="checkbox" class="checkbox mb-15" id="serviceType" value="<?php echo $servc['serviceID'] ?>" <?php echo $servc['is_check'] ? "checked" : "";?> onclick="return false;"/> <?php echo $servc['serviceType'] ?> <br>
										<?php } ?>
										
									</td>
								</tr>
								<tr>
									<td class="data-title w-10">Status:</td>
									<td class="w-20">
										<?php 
											if ($rec->status == 1) {
												echo "<span class='badge badge-pill badge-success'>Active</span>";
											} else {
												echo "<span class='badge badge-pill badge-danger'>Inactive</span>";
											}
										?>
									</td>
									<td class="d-xxl-none"></td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script>

	$(document).ready(function(){

		$("#checkAll").click(function(){
		    $('input:checkbox').not(this).not('#specific').prop('checked', this.checked);

    		$('input:checkbox').each(function(k, chekced){
    			    	
    			console.log(checked.value)

		        $.ajax({
		            url:"http://localhost/iwash/generic_ajax/get_category",
		            method: "POST",
		            data: { serviceID: $('#serviceID').val() },
		            dataType: "json",
		            success: function(data){
		            getCategory(serviceVal,serviceCount,data);
		           }
		       });
    		});

		});		
	});

	function assignCat(id){
		
		console.log(id)

 			var idSelector = function() { return this.id; };
    		var fruitsGranted = $(":checkbox:checked").map(idSelector).get() ;
    		alert('fruitsGranted2',fruitsGranted)
		
	}

</script>