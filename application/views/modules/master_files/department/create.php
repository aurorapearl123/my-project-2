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
					<form method="post" name="frmEntry" id="frmEntry" action="<?php echo site_url("department/save") ?>">
						<div class="table-row">
							<table class="table-form" >
								<tbody>
									<tr>
										<td class="form-label " width="200" >
											<label for="config">Company<span class="asterisk">*</span></label>
										</td>
										<td class="form-group form-input " width="400" >
											<?php 
												$this->db->where('status',1);
												$this->db->where('companyID',1);
												$companies = $this->db->get('companies')->result();
												
												$default_companyCode = $this->config_model->getConfig('Default Company Code');
												?>
											<select class="form-control" id="companyID" name="companyID" label="Company" style="width:auto;" required onchange="get_branches();">
												
												<?php foreach($companies as $com) {?>
												<option value="<?php echo $com->companyID ?>" <?php if ($com->companyCode == $default_companyCode) echo "selected"; ?>><?php echo $com->companyCode." - ".$com->companyName ?></option>
												<?php } ?>
											</select>
										</td>
										<td>&nbsp;</td>
									</tr>
									<tr>
										<td class="form-label " width="200" >
											<label for="config">Branch<span class="asterisk">*</span></label>
										</td>
										<td class="form-group form-input " width="400" >
											<?php 
												$this->db->where('status',1);
												$branches = $this->db->get('branches')->result();
												?>
											<select class="form-control selectpicker" id="branchID" name="branchID" style="width:auto;" label="Branch" required >
												<option value="">&nbsp;</option>
												<?php foreach($branches as $b) {?>
												<option value="<?php echo $b->branchID ?>" ><?php echo $b->branchCode." - ".$b->branchName ?></option>
												<?php } ?>
											</select>
										</td>
										<td>&nbsp;</td>
									</tr>
									<tr>
										<td class="form-label " width="200" >
											<label for="config">Dept Code<span class="asterisk">*</span></label>
										</td>
										<td class="form-group form-input ">
											<input style="width: 300px;" type="text" label="Dept Code" class="form-control" name="deptCode" id="deptCode" required  >
										</td>
										<td>&nbsp;</td>
									</tr>
									<tr>
										<td class="form-label align-text-top pt-5">
											<label for="occupation">Dept Name<span class="asterisk">*</span></label>
										</td>
										<td class="form-group form-input">
											<input style="width: 500px;" type="text" class="form-control" label="Department Name" name="deptName" id="deptName" required >
										</td>
										<td>&nbsp;</td>
									</tr>
									<tr>
										<td class="form-label align-text-top pt-5">
											<label for="occupation">Dept Head </label>
										</td>
										<td class="form-group form-input">
											<?php 
												$this->db->where('status',1);
												$heads = $this->db->get('employees')->result();
												?>
											<select class="form-control" id="deptHead" name="deptHead" data-live-search="true" livesearchnormalize="true" >
												<option value="">&nbsp;</option>
												<?php foreach($heads as $row) {?>
												<option value="<?php echo $row->empID ?>"><?php echo $row->lname.", ".$row->fname." ".$row->mname ?></option>
												<?php } ?>
											</select>
										</td>
										<td>&nbsp;</td>
									</tr>
									<tr>
										<td class="form-label align-text-top pt-5">
											<label for="occupation">Head Title </label>
										</td>
										<td class="form-group form-input">
											<input style="width: 500px;" type="text" class="form-control" title="Head Title" name="deptTitle" id="deptTitle">
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
	        $.post("<?php echo $controller_page ?>/check_duplicate", { companyID: $('#companyID').val(), branchID: $('#branchID').val(), deptCode: $('#deptCode').val() },
	          function(data){
	            if (parseInt(data)) {
	            	$('#cmdSave').removeClass("loader");
	            	$('#cmdSave').removeAttr('disabled');
	              	// duplicate
	              	swal("Duplicate","Record is already exist!","warning");
	            } else {
	            	// submit
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
		    	req_fields += "<br/>" + $(this).attr('label');
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
	
	<?php 
		echo "\n";
		$parameters = array('companyID');
		echo $this->htmlhelper->get_json_select('get_branches', $parameters, site_url('generic_ajax/get_branches'), 'branchID', '');
		?>
	
</script>
