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
					<form method="post" name="frmEntry" id="frmEntry" action="<?php echo site_url('plantilla/update') ?>">
						<input type="hidden" name="jobPositionID" id="jobPositionID" value="<?php echo $this->encrypter->encode($rec->jobPositionID) ?>" />
						<div class="table-row">
							<table class="table-form column-3">
								<tbody>
									<tr>
										<td class="form-label">Company <span class="asterisk">*</span></td>
										<td class="form-group form-input">
											<select id="companyID" name="companyID" class="form-control" title="Company" onchange="get_branches();" required>
												<option value="" selected>&nbsp;</option>
												<?php 
													$results = $this->db->get('companies')->result();
													foreach($results as $res){
													?>
												<option value="<?php echo $res->companyID?>" <?php if($res->companyID == $rec->companyID){ echo "selected";}?>><?php echo $res->companyCode.' - '.$res->companyName?></option>
												<?php }?>
											</select>
										</td>
										<td class="form-label">Branch <span class="asterisk">*</span></td>
										<td class="form-group form-input">
											<select id="branchID" name="branchID" class="form-control" title="Branch" onchange="get_departments();" required>
												<option value="" selected>&nbsp;</option>
												<?php 
													$results = $this->db->get('branches')->result();
													foreach($results as $res){
													?>
												<option value="<?php echo $res->branchID?>" <?php if($res->branchID == $rec->branchID){ echo "selected";}?>><?php echo $res->branchCode.' - '.$res->branchName?></option>
												<?php }?>
											</select>
										</td>
										<td class="d-xxl-none"></td>
									</tr>
									<tr>
										<td class="form-label" style="width:12%">Department <span class="asterisk">*</span></td>
										<td class="form-group form-input" style="width:21.33%">
											<select id="deptID" name="deptID" class="form-control" title="Department" onchange="get_sections();" required>
												<option value="" selected>&nbsp;</option>
												<?php 
													$results = $this->db->get('departments')->result();
													foreach($results as $res){
													?>
												<option value="<?php echo $res->deptID?>" <?php if($res->deptID == $rec->deptID){ echo "selected";}?>><?php echo $res->deptCode.' - '.$res->deptName?></option>
												<?php }?>
											</select>
										</td>
										<td class="form-label" style="width:12%">Section <span class="asterisk">*</span></td>
										<td class="form-group form-input" style="width:21.33%">
											<select id="divisionID" name="divisionID" class="form-control" title="Section" required>
												<option value="" selected>&nbsp;</option>
												<?php 
													$results = $this->db->get('divisions')->result();
													foreach($results as $res){
													?>
												<option value="<?php echo $res->divisionID?>" <?php if($res->divisionID == $rec->divisionID){ echo "selected";}?>><?php echo $res->divisionCode.' - '.$res->divisionName?></option>
												<?php }?>
											</select>
										</td>
										<td class="d-xxl-none"></td>
									</tr>
									<tr>
										<td class="form-label" style="width:120px" nowrap>Position No <span class="asterisk">*</span></td>
										<td class="form-group form-input" style="width:350px" nowrap>
											<input type="text" class="form-control" name="positionCode" id="positionCode" title="Position Code" value="<?php echo $rec->positionCode?>" required>
										</td>
										<td class="form-label" style="width:120px" nowrap>Job Title <span class="asterisk">*</span></td>
										<td class="form-group form-input" style="width:350px" nowrap>
											<select id="jobTitleID" name="jobTitleID" class="form-control" data-live-search="true" liveSearchNormalize="true" title="Job Title" required>
												<option value="" selected>&nbsp;</option>
												<?php 
													$results = $this->db->get('job_titles')->result();
													foreach($results as $res){
													?>
												<option value="<?php echo $res->jobTitleID?>" <?php if($res->jobTitleID == $rec->jobTitleID){ echo "selected";}?>><?php echo $res->code.' - '.$res->jobTitle?></option>
												<?php }?>
											</select>
										</td>
										<td class="d-xxl-none"></td>
									</tr>
									<tr>
										<td class="form-label" nowrap>Status <span class="asterisk">*</span></td>
										<td class="form-group form-input" nowrap>
											<select class="form-control" id="status" name="status" style="width:100px">
												<option value="">&nbsp;</option>
												<option value="1" <?php if($rec->status == "1") echo "selected"; ?>>Vacant</option>
												<option value="2" <?php if($rec->status == "2") echo "selected"; ?>>Occupied</option>
												<option value="0" <?php if($rec->status == "0") echo "selected"; ?>>Inactive</option>
											</select>
										</td>
										<td class="form-label" nowrap>
											<label for="occupation">&nbsp;</label>
										</td>
										<td class="form-group form-input" style="width:21.33%">&nbsp;</td>
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
		    	  window.location = '<?php echo site_url('plantilla/show') ?>';
		      }
		    });
	    
	});
	
	<?php 
		echo "\n";
		$parameters = array('companyID');
		echo $this->htmlhelper->get_json_select('get_branches', $parameters, site_url('generic_ajax/get_branches'), 'branchID', '');
		
		echo "\n";
		$parameters = array('branchID');
		echo $this->htmlhelper->get_json_select('get_departments', $parameters, site_url('generic_ajax/get_departments'), 'deptID', '');
		
		echo "\n";
		$parameters = array('deptID');
		echo $this->htmlhelper->get_json_select('get_sections', $parameters, site_url('generic_ajax/get_sections'), 'divisionID', '');
		?>
</script>
