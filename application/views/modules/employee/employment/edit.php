<div class="subheader">
  <div class="d-flex align-items-center">
    <div class="title mr-auto">
      <h3><i class="icon left la <?php echo $current_module['icon'] ?>"></i> Employments</h3>
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
          <form method="post" name="frmEntry" id="frmEntry" action="<?php echo $controller_page ?>/update" >

            
            <input type="hidden" name="<?php echo $pfield ?>" id="<?php echo $pfield ?>" value="<?php echo $rec->$pfield ?>" />
            <input type="hidden" id="empID" name="empID" value="<?php echo $rec->empID?>" />
            <input type="hidden" id="employmentNo" name="employmentNo" value="<?php echo $rec->employmentNo?>" />
            <input type="hidden" id="basicContributions" name="basicContributions" value="<?php echo $rec->basicContributions?>" />
            
            <input type="hidden" id="old_jobPositionID" name="old_jobPositionID" value="<?php echo $rec->jobPositionID?>" />
            <input type="hidden" id="old_withBasicContribution" name="old_withBasicContribution" value="<?php echo $rec->withBasicContribution?>" />


            <div class="table-row">


             <!-- Table Title -->
            <table class="table-form column-3">
              <tbody>
                <tr>
                  <td class="form-label" width="12%">
                    <label for="lname">Employee<span class="asterisk">*</span></label>
                  </td>
                  <td class="form-group form-input" width="21.33%">
                        <input type="text" class="form-control"  name="employeename" id="employeename" value="<?php echo $rec->lname.', '.$rec->fname.' '.$rec->mname ?>" disabled>
                  </td>
                  <td class="form-label" width="12%">
                    <label for="fname"></label>
                  </td>
                  <td class="form-group form-input" width="21.33%">
                  </td>
                  <td class="form-label" width="12%">
                    <label for="mname"></label>
                  </td>
                  <td class="form-group form-input" width="21.33%">
                  </td>
                </tr>
                <tr><td>&nbsp;</td></tr>
                <tr>
                  <td class="form-label">
                    Date Hired<span class="asterisk">*</span>
                  </td>
                  <td class="form-group form-input">
                    <input type="text" class="form-control datepicker" name="dateAppointed" id="dateAppointed" value="<?php echo ($rec->dateAppointed!="0000-00-00") ? date('m/d/Y', strtotime($rec->dateAppointed)) : ""; ?>" data-toggle="datetimepicker" data-target="#dateAppointed" required>
                  </td>
                   <td class="form-label">
                    Branch<span class="asterisk">*</span>
                  </td>
                  <td class="form-group form-input">
                    <select id="branchID" name="branchID" class="form-control" onchange="get_departments(); getPayrollGroups();"  title="Branch" required>
                    <option value="" selected>&nbsp;</option>
                      <?php           
                      $this->db->where('status', 1);
                      $this->db->order_by('branchName','asc');
                      $results = $this->db->get('branches')->result();
                      foreach($results as $res){
                        ?>
                        <option value="<?php echo $res->branchID ?>" <?php if ($rec->branchID == $res->branchID) echo "selected"; ?>><?php echo $res->branchCode ?></option>
                      <?php }?>
                    </select>
                  </td>
                </tr>
               <tr>
                  <td class="form-label">
                    Department<span class="asterisk">*</span>
                  </td>
                  <td class="form-group form-input">
                    <select id="deptID" name="deptID" class="form-control" onchange="get_sections();"  title="Department" required>
                    <option value="" selected>&nbsp;</option>
                      <?php           
                      $this->db->where('branchID', $rec->branchID);
                      $this->db->where('status', 1);
                      $this->db->order_by('deptName','asc');
                      $results = $this->db->get('departments')->result();
                      foreach($results as $res){
                        ?>
                        <option value="<?php echo $res->deptID ?>" <?php if ($rec->deptID == $res->deptID) echo "selected"; ?>><?php echo $res->deptCode ?></option>
                      <?php }?>
                    </select>
                  </td>
                  <td class="form-label">
                    Section<span class="asterisk">*</span>
                  </td>
                  <td class="form-group form-input">
                    <select id="divisionID" name="divisionID" class="form-control" data-live-search="true" liveSearchNormalize="true"  title="Section" required onchange="get_plantilla();">
                    <option value="" selected>&nbsp;</option>
                      <?php       
                      $this->db->where('branchID', $rec->branchID);
                      $this->db->where('deptID', $rec->deptID);
                      $this->db->where('status', 1);
                      $this->db->order_by('divisionName','asc');
                      $results = $this->db->get('divisions')->result();
                      foreach($results as $res){
                        ?>
                        <option value="<?php echo $res->divisionID ?>" <?php if ($rec->divisionID == $res->divisionID) echo "selected"; ?>><?php echo $res->divisionName ?></option>
                      <?php }?>
                    </select>
                  </td>
                </tr>
                <tr>
                  <td class="form-label" nowrap>
                    Employment Type<span class="asterisk">*</span>
                  </td>
                  <td class="form-group form-input">
                    <select id="employeeTypeID" name="employeeTypeID" class="form-control"  title="Employment Type" required>
                    <option value="" selected>&nbsp;</option>
                      <?php           
                      $this->db->where('status', 1);
                      $this->db->order_by('employeeType','asc');
                      $results = $this->db->get('employee_types')->result();
                      foreach($results as $res){
                        ?>
                        <option value="<?php echo $res->employeeTypeID ?>" <?php if ($rec->employeeTypeID == $res->employeeTypeID) echo "selected"; ?>><?php echo $res->employeeType ?></option>
                      <?php }?>
                    </select>
                  </td>
                  <td class="form-label" nowrap>
                    Job Position<span class="asterisk">*</span>
                  </td>
                  <td class="form-group form-input">
                    <select id="jobPositionID" name="jobPositionID" class="form-control" data-live-search="true" liveSearchNormalize="true" title="Job Position"  required>
                        <option value="<?php echo $rec->jobPositionID ?>" selected><?php echo $rec->jobTitle." (".$rec->positionCode.")" ?></option>
                        <?php      
                          $this->db->select('job_positions.*');
                          $this->db->select('job_titles.jobTitle');
                          $this->db->from('job_positions');
                          $this->db->join('job_titles','job_positions.jobTitleID=job_titles.jobTitleID','left');
                          $this->db->where('job_positions.branchID', $rec->branchID);
                          $this->db->where('job_positions.deptID', $rec->deptID);
                          $this->db->where('job_positions.divisionID', $rec->divisionID);
                          $this->db->where('job_positions.status',1);
                          $this->db->order_by('job_titles.jobTitle','asc');
                          $results = $this->db->get()->result();
                          foreach($results as $res){
                              if ($res->jobPositionID != $rec->jobPositionID) {
                        ?>
                        <option value="<?php echo $res->jobPositionID ?>"><?php echo $res->jobTitle." (".$res->positionCode.")" ?></option>
                      <?php }
                          }
                      ?>
                    </select>
                    
                  </td>
                  <td>&nbsp;</td>
                </tr>
                <tr>
                  <td class="form-label" valign="top">Basic Salary</td>
                  <td class="form-group form-input">
                    <table width="106%" style="margin-left: -10px;">
                        <tr>
                            <td><input type="text" class="form-control"  name="basicSalary" id="basicSalary" value="<?php echo number_format($rec->basicSalary,2,".","") ?>" required style="width: 150px;"></td>
                            <td>
                                <select name="salaryType" id="salaryType" class="form-control" >
                                  <option value="1">Monthly</option>
                                  <option value="2">Daily</option>
                                  <option value="3">Hourly</option>
                                </select>
                            </td>
                        </tr>
                   </table>
                  </td>
                  <td class="form-label">&nbsp;</td>
                  <td class="form-group form-input">&nbsp;</td>
                </tr>
                <tr>
                  <td class="form-label" valign="top">Payroll Group</td>
                  <td class="form-group form-input">
                    <?php           
          $this->db->where('companyID', 1);
          $this->db->where('branchID', $rec->branchID);
          $this->db->where('status', 1);
          $this->db->order_by('rank','asc');
      $this->db->order_by('payrollGroup','asc');
      $data = $this->db->get('payroll_groups');
      
      echo $this->htmlhelper->select_object('payrollGroupID', $data, 'payrollGroupID', 'payrollGroup', 301, $rec->payrollGroupID, '', '',' tabindex="75"');
      ?>
                  </td>
                  <td class="form-label"></td>
                  <td class="form-group form-input">

                  </td>
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
	    	  window.location = '<?php echo site_url('employment/view/'.$this->encrypter->encode($rec->employmentID)) ?>';
	      }
	    });
    
});



<?php 
    echo "\n";
    $parameters = array('branchID');
    echo $this->htmlhelper->get_json_select('get_departments', $parameters, site_url('generic_ajax/get_code_departments'), 'deptID', '');
    
    echo "\n";
    $parameters = array('deptID');
    echo $this->htmlhelper->get_json_select('get_sections', $parameters, site_url('generic_ajax/get_code_sections'), 'divisionID', '');
    
    echo "\n";
    $parameters = array('branchID','deptID','divisionID');
    echo $this->htmlhelper->get_json_select('get_plantilla', $parameters, site_url('generic_ajax/get_plantilla'), 'jobPositionID', '');

    echo "\n";
    $parameters = array('branchID');
    echo $this->htmlhelper->get_json_select('getPayrollGroups', $parameters, site_url('payroll_group/getPayrollGroups'), 'payrollGroupID', 'activeID') ;
?>
</script>






















