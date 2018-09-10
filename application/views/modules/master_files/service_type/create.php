<div class="subheader">
	<div class="d-flex align-items-center">
		<div class="title mr-auto">
			<h3><i class="icon left la <?php echo $current_module['icon'] ?>"></i> <?php echo $current_module['title'] ?></h3>
		</div>
		<div class="subheader-tools"></div>
	</div>
</div>
<div class="content">
	<div class="row">
		<div class="col-12">
			<div class="card-box">
				<div class="card-head">
					<div class="head-caption">
						<div class="head-title">
							<h4 class="head-text">Add <?php echo $current_module['module_label'] ?></h4>
						</div>
					</div>
					<div class="card-head-tools"></div>
				</div>
				<div class="card-body">
					<form method="post" name="frmEntry" id="frmEntry" action="<?php echo site_url("service_type/save") ?>">
						<div class="table-row">
							<table class="table-form">
								<tbody>
									<tr>
										<td class="form-label" width="13%" nowrap>Service Type : <span class="asterisk">*</span></td>
										<td class="form-group form-input" width="22%">
											<input type="text" class="form-control" name="serviceType" id="serviceType" title="Service Type" required>
										</td>
										<!-- <td class="form-label" width="13%"nowrap>Regular Rate : <span class="asterisk">*</span></td>
										<td class="form-group form-input" width="22%">
											<input type="text" class="form-control" name="regRate" id="regRate" title="Regular Rate" required>
										</td> -->
										<td class="d-xxl-none"></td>
									</tr>

								<!-- 	<tr>
										<td class="form-label" nowrap>Discounted Rate : <span class="asterisk">*</span></td>
										<td class="form-group form-input">
											<input type="text" class="form-control" name="discountedRate" id="discountedRate" title="Discounted Rate" required>
										</td>
										<td class="d-xxl-none" colspan="3"></td>
									</tr> -->

								
								</tbody>
							</table>
						</div>
						<div class="form-sepator solid"></div>
						<div class="form-group mb-0">
							<button class="btn btn-primary btn-raised pill" type="button" name="cmdSave" id="cmdSave">
							Save
							</button>
							<input type="button" id="cmdCancel" class="btn btn-outline-danger btn-raised pill" value="Cancel"/>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
<script>
	$('#cmdSave').click(function(){
		if (check_fields()) {
	    	$('#cmdSave').attr('disabled','disabled');
	    	$('#cmdSave').addClass('loader');
	        $.post("<?php echo $controller_page ?>/check_duplicate", { serviceType: $('#serviceType').val() },
	          function(data){
	            if (parseInt(data)) {
	            	$('#cmdSave').removeClass("loader");
	            	$('#cmdSave').removeAttr('disabled');
	              	// duplicate
	              	swal("Duplicate","Record is already exist!","warning");
	            } else {
	     //        	// submit
	               	$('#frmEntry').submit();
	            }
	          }, "text");
		}
	});
	
	
	function check_fields()
	{
		 var valid = true;
		 var req_fields = "";
		 
		 $('#frmEntry [required]').each(function(){
		    if($(this).val()=='' ) {
		    	req_fields += "<br/>" + $(this).attr('title');
			    valid = false;
		    } 
		 })
		 
		 if (!valid) {
		 	swal("Required Fields",req_fields,"warning");
		 }
		 
		 return valid;
	}
	
	$('#cmdCancel').click(function(){
		swal({
		      title: "Are you sure?",
		      text: "",
		      icon: "warning",
		      showCancelButton: true,
		      confirmButtonColor: '#3085d6',
		      cancelButtonColor: '#d33',
		      confirmButtonText: 'Yes',
		      cancelButtonText: 'No'
		    })
		    .then((willDelete) => {
		      if (willDelete.value) {
		    	  window.location = '<?php echo site_url('service_type/show') ?>';
		      }
		    });
	    
	});
</script>