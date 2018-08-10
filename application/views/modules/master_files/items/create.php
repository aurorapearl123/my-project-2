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
					<form method="post" name="frmEntry" id="frmEntry" action="<?php echo site_url("item/save") ?>">
						<div class="table-row">
							<table class="table-form column-3">
								<tbody>
									<tr>
										<td class="form-label" width="120" nowrap>Brand <span class="asterisk">*</span></td>
										<td class="form-group form-input" width="350">
											<input type="text" class="form-control" name="brand" id="brand" title="Brand" required>
										</td>
										<td class="d-xxl-none" colspan="2"></td>
									</tr>

									<tr>
										<td class="form-label" nowrap>Item <span class="asterisk">*</span></td>
										<td class="form-group form-input">
											<input type="text" class="form-control" name="item" id="item" title="Item" required>
										</td>
										<td class="d-xxl-none" colspan="2"></td>
									</tr>
									<tr>
										<td class="form-label" nowrap>Description <span class="asterisk">*</span></td>
										<td class="form-group form-input">											
											<textarea rows="2" type="text" class="form-control" name="description" id="description" title="Description" required></textarea>
										</td>
										<td class="d-xxl-none" colspan="2"></td>
									</tr>

									<tr>
										<td class="form-label" nowrap>UMSR <span class="asterisk">*</span></td>
										<td class="form-group form-input">
											<input type="text" class="form-control" name="umsr" id="umsr" title="UMSR" required>
										</td>
										<td class="d-xxl-none" colspan="2"></td>
									</tr>

								
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
	        $.post("<?php echo $controller_page ?>/check_duplicate", { brand: $('#brand').val() },
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
		    	  window.location = '<?php echo site_url('item/show') ?>';
		      }
		    });
	    
	});
</script>