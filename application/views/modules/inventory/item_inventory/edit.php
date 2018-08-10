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
					<form method="post" name="frmEntry" id="frmEntry" action="<?php echo site_url('order/update') ?>">
						<input type="hidden" name="companyID" id="companyID" value="<?php echo $this->encrypter->encode($rec->companyID) ?>" />
						<div class="table-row">
							<table class="table-form column-3">
								<tbody>
									<tr>
										<td class="form-label" nowrap>Company Code <span class="asterisk">*</span></td>
										<td class="form-group form-input">
											<input type="text" class="form-control" name="companyCode" id="companyCode" value="<?php echo $rec->companyCode ?>" title="Company Code" required>
										</td>
										<td class="d-xxl-none" colspan="3"></td>
									</tr>
									<tr>
										<td class="form-label" style="width:120px" nowrap>Company Name <span class="asterisk">*</span></td>
										<td class="form-group form-input" style="width:320px" nowrap>
											<input type="text" class="form-control" name="companyName" id="companyName" value="<?php echo $rec->companyName ?>" title="Company Name" required>
										</td>
										<td class="form-label" style="width:120px" nowrap>Company Abbr <span class="asterisk">*</span></td>
										<td class="form-group form-input" style="width:320px">
											<input type="text" class="form-control" name="companyAbbr" id="companyAbbr" value="<?php echo $rec->companyAbbr ?>" title="Company Abbr" required>
										</td>
										<td class="d-xxl-none"></td>
									</tr>
									<tr>
										<td class="form-label" nowrap>Company Contact <span class="asterisk">*</span></td>
										<td class="form-group form-input">
											<input type="text" class="form-control" name="companyContact" id="companyContact" value="<?php echo $rec->companyContact ?>" title="Company Contact" required>
										</td>
										<td class="form-label" nowrap>Company Email </td>
										<td class="form-group form-input">
											<input type="text" class="form-control" name="companyEmail" id="companyEmail" value="<?php echo $rec->companyEmail ?>">
										</td>
										<td class="d-xxl-none"></td>
									</tr>
									<tr>
										<td class="form-label" nowrap>Company Head <span class="asterisk">*</span></td>
										<td class="form-group form-input">
											<?php 
												$this->db->where('status',1);
												$heads = $this->db->get('employees')->result();
												?>
											<select class="form-control" id="companyHeadID" name="companyHeadID" data-live-search="true" livesearchnormalize="true"  title="Company Head" required>
												<option value="" selected>&nbsp;</option>
												<?php foreach ($heads as $row) {?>
												<option value="<?php echo $row->empID ?>" <?php if ($rec->companyHeadID == $row->empID) { echo "selected"; } ?>><?php echo $row->lname.", ".$row->fname." ".$row->mname ?></option>
												<?php } ?>
											</select>
										</td>
										<td class="form-label" nowrap>Head Title <span class="asterisk">*</span></td>
										<td class="form-group form-input">
											<input type="text" class="form-control" name="companyHeadTitle" id="companyHeadTitle" value="<?php echo $rec->companyHeadTitle?>" title="Head Title" required>
										</td>
										<td class="d-xxl-none"></td>
									</tr>
									<tr>
										<td class="form-label align-text-top pt-5" nowrap>Company Address <span class="asterisk">*</span></td>
										<td class="form-group form-input">
											<textarea class="form-control" name="companyAddress" id="companyAddress" title="Company Address" required><?php echo $rec->companyAddress?></textarea>
										</td>
										<td class="form-label align-text-top pt-5" nowrap>&nbsp;</td>
										<td class="form-group form-input">
											<!-- <textarea class="form-control" name="remarks" id="remarks"><?php //echo $rec->remarks?></textarea> -->
										</td>
										<td class="d-xxl-none"></td>
									</tr>
									<tr>
										<td class="form-label" nowrap>Status <span class="asterisk">*</span></td>
										<td class="form-group form-input">
											<select id="status" name="status" class="form-control" title="Status" required>
												<option value="" selected>&nbsp;</option>
												<option value="1" <?php if ($rec->status==1) { echo "selected"; } ?>>Active</option>
												<option value="0" <?php if ($rec->status==0) { echo "selected"; } ?>>Inactive</option>
											</select>
										</td>
										<td class="d-xxl-none" colspan="3"></td>
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
	    	  window.location = '<?php echo site_url('order/show') ?>';
	      }
	    });
	});
</script>
