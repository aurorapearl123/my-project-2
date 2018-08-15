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
							<h4 class="head-text">Edit <?php echo $current_module['module_label'] ?></h4>
						</div>
					</div>
					<div class="card-head-tools"></div>
				</div>
				<div class="card-body">
					<form method="post" name="frmEntry" id="frmEntry" action="<?php echo site_url('item_inventory/update') ?>">
						<input type="hidden" name="invID" id="invID" value="<?php echo $this->encrypter->encode($rec->invID) ?>" />
						<div class="table-row">
							<table class="table-form column-3">
								<tbody>
									<tr>
										<td class="form-label" width="100" nowrap>Branch <span class="asterisk">*</span></td>
										<td class="form-group form-input" width="350" nowrap>
											<input type="text" class="form-control" name="branchName" id="branchName" value="<?php echo $rec->branchName ?>" title="Branch" required readonly="readonly">
										</td>	
										<td></td>									
									</tr>
									<tr>
										<td class="form-label" style="" nowrap>Brand <span class="asterisk">*</span></td>
										<td class="form-group form-input"  nowrap>
											<input type="text"  class="form-control" name="brand" id="brand" value="<?php echo $rec->brand ?>" title="Brand" required readonly="readonly">
										</td>
										<td></td>
									</tr>
									<tr>
										<td class="form-label" nowrap>Item <span class="asterisk">*</span></td>
										<td class="form-group form-input">
											<input type="text" class="form-control" name="item" id="item" value="<?php echo $rec->item ?>" title="Item" required readonly="readonly">
										</td>
										<td></td>
									</tr>									
									<tr>
										<td class="form-label" nowrap>Description <span class="asterisk">*</span></td>
										<td class="form-group form-input">
											<input type="text" class="form-control" name="description" id="description" value="<?php echo $rec->description ?>" title="Description" required readonly="readonly">
										</td>
										<td></td>
									</tr>									
									<tr>
										<td class="form-label" nowrap>Reorder Level <span class="asterisk">*</span></td>
										<td class="form-group form-input">
											<input type="text" class="form-control" name="reorderlvl" id="reorderlvl" value="<?php echo $rec->reorderlvl ?>" title="Reorder Level" required >
										</td>
										<td></td>
									</tr>
									<tr>
										<td class="form-label" nowrap>QTY <span class="asterisk">*</span></td>
										<td class="form-group form-input">
											<input type="text" class="form-control" name="qty" id="qty" value="<?php echo $rec->qty ?>" title="QTY" required readonly="readonly">
										</td>
										<td></td>
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
	       	$('#frmEntry').submit();
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
	    	  window.location = '<?php echo site_url('item_inventory/show') ?>';
	      }
	    });
	});
</script>
