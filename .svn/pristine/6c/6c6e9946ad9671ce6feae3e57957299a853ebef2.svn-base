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
					<form method="post" name="frmEntry" id="frmEntry" action="<?php echo site_url('employment_type/save') ?>">
						<div class="table-row">
							<table class="table-form">
							<tbody>
								<tr>
									<td class="data-title" nowrap>Type:</td>
									<td class="data-input" nowrap><input class="form-control" type="text" name="employeeType" id="employeeType" value="<?php echo $rec->employeeType?>" maxlength="100" /></td>
									<td colspan="3"></td>
								</tr>
								<tr>
									<td class="data-title" style="width:120px" nowrap>Salary Type:</td>
									<td class="data-input" style="width:320px" nowrap>
                            		<select class="form-control" name="salaryType" id="salaryType" style="width:156px">
                                   		<option value=""></option>
                                   		<option value="1">Monthly</option>
                                    	<option value="2">Daily</option>
                                    	<option value="3">Hourly</option>
                            		</select> 
									</td>
									<td class="data-title" style="width:120px" nowrap>Default Salary:</td>
									<td class="data-input" style="width:320px" nowrap><input class="form-control" type="text" name="defaultSalary" id="defaultSalary" value="0.00" maxlength="20" onkeypress='return event.charCode >= 48 && event.charCode <= 57'/></td>
									<td></td>
								</tr>
								<tr>
									<td class="data-title" nowrap>Working Days:</td>
									<td class="data-input" nowrap><input class="form-control" type="text" name="workingDays" id="workingDays" value="0.00" maxlength="20" onkeypress='return event.charCode >= 48 && event.charCode <= 57'/></td>
									<td class="data-title" nowrap>Days With Pay:</td>
									<td class="data-input" nowrap>
									<?php $withPay = explode(',', $rec->withPay)?>
                            		<select class="form-control" name="withPay[]" id="withPay" multiple>
                            			<option value="WD">Whole Day Work</option>
                                        <option value="HDA">Half Day Work Morning</option>
                                        <option value="HDP">Half Day Work Afternoon</option>
                                        <option value="RH">Regular Holiday</option>
                                        <option value="SH">Special Holiday</option>
                                        <option value="NW">No Work</option>
                            		</select>
									</td>
									<td></td>
								</tr>
								<tr>
								<td class="data-title" valign="top" nowrap>Basic Contributions:</td>
								<td class="data-input" nowrap>
                            		<select class="form-control" name="basicContributions[]" id="basicContributions" multiple>
                            		<option value=""></option>
                            		<?php 
                            		$this->db->where('status',1);
                            		$this->db->order_by('rank','asc');
                            		$this->db->order_by('classification','asc');
                            		$types = $this->db->get('pay_classifications');
                            		
                            		$basicContributions = explode(",",$rec->basicContributions);
                            		if ($types->num_rows()) {
                            			foreach ($types->result() as $type) {
                            				$this->db->where('classID', $type->classID);
                            				$this->db->where('isBasic', 1);
                            				$this->db->where('status', 1);
                            				$this->db->order_by('rank','asc');
                            				$this->db->order_by('name','asc');
                            				$data = $this->db->get('premiums');
                            				
                            				echo "<optgroup label=".$type->classification.">";
                            				if ($data->num_rows()) {
                            					foreach ($data->result() as $row) {
                            							echo "<option value='$row->premiumID'>$row->name</option>";
                            					}
                            				}
                            			}
                            		}				
                            		?>
                            		</select>
                                </td>
                                </tr>
								<tr>
									<td class="data-title" valign="top"  nowrap>Remarks:</td>
									<td class="data-input" nowrap><textarea	class="form-control" name="remarks" id="remarks" maxlength="50"></textarea></td>
									<td class="data-title" nowrap></td>
									<td></td>
									<td></td>
								</tr>
								

							</tbody>
							</table>
						</div>
						<div class="form-sepator solid"></div>
						<div class="form-group mb-0">
							<button class="btn btn-xs btn-primary btn-raised pill" type="button" name="cmdSave" id="cmdSave">
							Save
							</button>
							<input type="button" id="cmdCancel" class="btn btn-xs btn-outline-danger btn-raised pill" value="Cancel"/>
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
		    	  window.location = '<?php echo site_url('employment_type/show') ?>';
		      }
		    });
	    
	});
</script>
