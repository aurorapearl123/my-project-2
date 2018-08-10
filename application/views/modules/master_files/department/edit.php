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
					<form method="post" name="frmEntry" id="frmEntry" action="<?php echo site_url('department/update') ?>">
						<input type="hidden" name="deptID" id="deptID" value="<?php echo $this->encrypter->encode($rec->deptID) ?>" />
						<div class="table-row">
							<table class="table-form">
								<tbody>
									<tr>
										<td class="form-label " width="180" >
											Company<span class="asterisk">*</span>
										</td>
										<td class="form-group form-input " width="400" >
											<?php 
												$this->db->where('status',1);
												$companies = $this->db->get('companies')->result();
												
												$default_companyCode = $this->config_model->getConfig('Default Company Code');
												?>
											<select class="form-control" id="companyID" name="companyID" style="width:auto;" required >
												<option value="">&nbsp;</option>
												<?php foreach($companies as $com) {?>
												<option value="<?php echo $com->companyID ?>" <?php if ($com->companyID == $rec->companyID) echo "selected"; ?>><?php echo $com->companyCode." - ".$com->companyName ?></option>
												<?php } ?>
											</select>
										</td>
										<td>&nbsp;</td>
									</tr>
									<tr>
										<td class="form-label " width="200" >
											Branch<span class="asterisk">*</span>
										</td>
										<td class="form-group form-input " width="400" >
											<?php 
												$this->db->where('status',1);
												$branches = $this->db->get('branches')->result();
												?>
											<select class="form-control" id="branchID" name="branchID" style="width:auto;" required >
												<option value="">&nbsp;</option>
												<?php foreach($branches as $b) {?>
												<option value="<?php echo $b->branchID ?>" <?php if ($b->branchID == $rec->branchID) echo "selected"; ?> ><?php echo $b->branchCode." - ".$b->branchName ?></option>
												<?php } ?>
											</select>
										</td>
										<td>&nbsp;</td>
									</tr>
									<tr>
										<td class="form-label align-text-top pt-5">
											Dept. Code<span class="asterisk">*</span>
										</td>
										<td class="form-group form-input">
											<input type="text" class="form-control" name="deptCode" id="deptCode" value="<?php echo $rec->deptCode ?>" required>
										</td>
										<td>&nbsp;</td>
									</tr>
									<tr>
										<td class="form-label align-text-top pt-5">
											Department Name<span class="asterisk">*</span>
										</td>
										<td class="form-group form-input">
											<input style="width: 500px;" type="text" class="form-control" title="Department Name" name="deptName" id="deptName" value="<?php echo $rec->deptName ?>" required >
										</td>
										<td>&nbsp;</td>
									</tr>
									<tr>
										<td class="form-label align-text-top pt-5">
											<label for="occupation">Dept Head</label>
										</td>
										<td class="form-group form-input">
											<?php 
												$this->db->where('status',1);
												$heads = $this->db->get('employees')->result();
												?>
											<select class="form-control" id="deptHead" title="Department Head" name="deptHead" data-live-search="true" livesearchnormalize="true">
												<option value="">&nbsp;</option>
												<?php foreach($heads as $row) {?>
												<option value="<?php echo $row->empID ?>" <?php if ($row->empID == $rec->deptHead) echo "selected"; ?>><?php echo $row->lname.", ".$row->fname." ".$row->mname ?></option>
												<?php } ?>
											</select>
										</td>
										<td>&nbsp;</td>
									</tr>
									<tr>
										<td class="form-label align-text-top pt-5">
											<label for="occupation">Head Title</label>
										</td>
										<td class="form-group form-input">
											<input style="width: 500px;" type="text" class="form-control" title="Head Title" name="deptTitle" id="deptTitle" value="<?php echo $rec->deptTitle ?>">
										</td>
										<td>&nbsp;</td>
									</tr>
									<tr>
										<td class="form-label align-text-top pt-5">
											Status<span class="asterisk">*</span>
										</td>
										<td class="form-group form-input">
											<select id="status" name="status" class="form-control" data-live-search="true" liveSearchNormalize="true" required>
												<option value="1" <?php if ($rec->status == 1) echo "selected"; ?>>Active</option>
												<option value="0" <?php if ($rec->status == 0) echo "selected"; ?>>Inactive</option>
											</select>
										</td>
										<td>&nbsp;</td>
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
		    	  window.location = '<?php echo site_url('department/show') ?>';
		      }
		    });
	    
	});
</script>
