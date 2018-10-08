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
					<form method="post" name="frmEntry" id="frmEntry" action="<?php echo site_url('branch/update') ?>">
						<input type="hidden" name="branchID" id="branchID" value="<?php echo $this->encrypter->encode($rec->branchID) ?>" />
						<div class="table-row">
							<table class="table-form">
								<tbody>
									<tr>
										<td class="form-label" width="13%" nowrap>Branch Code <span class="asterisk">*</span></td>
										<td class="form-group form-input" width="22%">
											<input type="text" class="form-control" name="branchCode" id="branchCode" value="<?php echo $rec->branchCode?>" title="Branch Code" required>
										</td>
										<td class="form-label" width="13%" nowrap>Company <span class="asterisk">*</span></td>
										<td class="form-group form-input" width="22%">
											<select id="companyID" name="companyID" class="form-control" title="Company" required>
												<option value="" selected>&nbsp;</option>
												<?php 
													$companies = $this->db->get('companies')->result();
													foreach($companies as $company) {
													?>
												<option value="<?php echo $company->companyID ?>" <?php if ($company->companyID == $rec->companyID) { echo "selected"; }?>><?php echo $company->companyCode.' - '.$company->companyName ?></option>
												<?php } ?>
											</select>
										</td>
										<td class="d-xxl-none"></td>
									</tr>
									<tr>
										<td class="form-label" nowrap>Branch Name <span class="asterisk">*</span></td>
										<td class="form-group form-input">
											<input type="text" class="form-control" name="branchName" id="branchName" value="<?php echo $rec->branchName ?>" title="Branch Name" required>
										</td>
										<td class="form-label" nowrap>Branch Abbr <span class="asterisk">*</span></td>
										<td class="form-group form-input">
											<input type="text" class="form-control" name="branchAbbr" id="branchAbbr" value="<?php echo $rec->branchAbbr ?>" title="Branch Abbr" required>
										</td>
										<td class="d-xxl-none"></td>
									</tr>
									<tr>
										<td class="form-label" nowrap>Branch Contact <span class="asterisk">*</span></td>
										<td class="form-group form-input">
											<input type="text" class="form-control" name="branchContact" id="branchContact" value="<?php echo $rec->branchContact ?>" title="Branch Contact" required>
										</td>
										<td class="form-label" nowrap>Branch Email </td>
										<td class="form-group form-input">
											<input type="text" class="form-control" name="branchEmail" id="branchEmail" value="<?php echo $rec->branchEmail ?>">
										</td>
										<td class="d-xxl-none"></td>
									</tr>
									<tr>
										<td class="form-label" nowrap>Branch Head </td>
										<td class="form-group form-input">
											<?php 
												$this->db->where('status',1);
												$heads = $this->db->get('employees')->result();
												?>
											<select class="form-control" id="branchHeadID" name="branchHeadID" data-live-search="true" livesearchnormalize="true" style="width:200px" title="Branch Head">
												<option value="" selected>&nbsp;</option>
												<?php foreach($heads as $row) { ?>
												<option value="<?php echo $row->empID ?>" <?php if ($row->empID == $rec->branchHeadID) { echo "selected"; } ?>><?php echo $row->lname.", ".$row->fname." ".$row->mname ?></option>
												<?php } ?>
											</select>
										</td>
										<td class="form-label" nowrap>Head Title </td>
										<td class="form-group form-input">
											<input type="text" class="form-control" name="branchHeadTitle" id="branchHeadTitle" value="<?php echo $rec->branchHeadTitle ?>" title="Head Title">
										</td>
										<td class="d-xxl-none"></td>
									</tr>
									<tr>
										<td class="form-label" nowrap>Admin Officer </td>
										<td class="form-group form-input">
											<input type="text" class="form-control" name="adminOfficer" id="adminOfficer" value="<?php echo $rec->adminOfficer ?>">
										</td>
										<td class="form-label" nowrap>Timekeeper </td>
										<td class="form-group form-input">
											<input type="text" class="form-control" name="timekeeper" id="timekeeper" value="<?php echo $rec->timekeeper ?>">
										</td>
										<td class="d-xxl-none"></td>
									</tr>
									<tr>
										<td class="form-label align-text-top pt-5" nowrap>Branch Address : <span class="asterisk">*</span></td>
										<td class="form-group form-input">
											<textarea class="form-control" name="branchAddress" id="branchAddress" title="Branch Address" required><?php echo $rec->branchAddress ?></textarea>
										</td>
										<td class="form-label align-text-top pt-5" nowrap>Status <span class="asterisk">*</span></td>
										<td class="form-group form-input align-text-top pt-5">
											<select id="status" name="status" class="form-control" title="Status" required>
												<option value="">&nbsp;</option>
												<option value="1" <?php if($rec->status==1){ echo "selected"; } ?>>Active</option>
												<option value="0" <?php if($rec->status==0){ echo "selected"; } ?>>Inactive</option>
											</select>
										</td>
										<td class="d-xxl-none"></td>
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
		    	  window.location = '<?php echo site_url('branch/show') ?>';
		      }
		    });
	    
	});
</script>
