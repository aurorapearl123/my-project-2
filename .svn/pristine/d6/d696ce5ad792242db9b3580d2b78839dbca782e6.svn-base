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
          <form method="post" name="frmEntry" id="frmEntry" action="<?php echo $controller_page ?>/save" >
            <div class="table-row">

             <!-- Table Title -->
             <div class="subtitle">
              <h5 class="title"><i class="icon left la <?php echo $current_module['icon'] ?>"></i> Employment Information</h5>
            </div>
            <table class="table-form column-3">
              <tbody>
                <tr>
                  <td class="form-label" width="12%">
                    Employee<span class="asterisk">*</span>
                  </td>
                  <td class="form-group form-input" width="21.33%">
                    <select name="empID" id="empID" class="form-control" data-live-search="true" liveSearchNormalize="true" title="Employee" required>
                    <option value="" selected>&nbsp;</option>
                      <?php           
                        $this->db->where('status', 1);
                          $results = $this->db->get('employees')->result();
                          foreach($results as $res){
                            ?>
                        <option value="<?php echo $res->empID ?>"><?php echo $res->lname.', '.$res->fname.' '.$res->mname." (".$res->empNo.")" ?></option>
                      <?php }?>
                    </select>
                  </td>
                  <td class="form-label" width="12%">
                  </td>
                  <td class="form-group form-input" width="21.33%">
                  </td>
                  <td class="form-label" width="12%">
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
                    <input type="text" class="form-control datepicker" name="dateAppointed" id="dateAppointed" value="" data-toggle="datetimepicker" data-target="#dateAppointed"  title="Date Hired" required>
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
                        <option value="<?php echo $res->branchID ?>"><?php echo $res->branchCode ?></option>
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
                      $this->db->where('status', 1);
                      $this->db->order_by('deptName','asc');
                      $results = $this->db->get('departments')->result();
                      foreach($results as $res){
                        ?>
                        <option value="<?php echo $res->deptID ?>"><?php echo $res->deptCode ?></option>
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
                      $this->db->where('status', 1);
                      $this->db->order_by('divisionName','asc');
                      $results = $this->db->get('divisions')->result();
                      foreach($results as $res){
                        ?>
                        <option value="<?php echo $res->divisionID ?>"><?php echo $res->divisionName ?></option>
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
                        <option value="<?php echo $res->employeeTypeID ?>"><?php echo $res->employeeType ?></option>
                      <?php }?>
                    </select>
                  </td>
                  <td class="form-label" nowrap>
                    Job Position<span class="asterisk">*</span>
                  </td>
                  <td class="form-group form-input">
                    <select id="jobPositionID" name="jobPositionID" class="form-control" data-live-search="true" liveSearchNormalize="true" title="Job Position" required>
                        <option value="" selected>&nbsp;</option>
                    </select>
                    
                  </td>
                  <td>&nbsp;</td>
                </tr>
                <tr>
                  <td class="form-label" valign="top">Basic Salary</td>
                  <td class="form-group form-input">
                    <table width="106%" style="margin-left: -10px;">
                        <tr>
                            <td><input type="text" class="form-control"  name="basicSalary" id="basicSalary" value="0.00" required style="width: 150px;"></td>
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
                  <td class="form-label">With Basic Benefits</td>
                  <td class="form-group form-input">
                        <div class="switch">
                          <input type="checkbox" name="withBasicContribution" id="withBasicContribution" value="1" /><span class="bmd-switch-track"><div class="ripple-container"></div></span>
                      </div>
                  </td>
                </tr>
                <tr>
                  <td class="form-label" valign="top">Payroll Group</td>
                  <td class="form-group form-input">
                    <?php 
                      $this->db->where('status',1); 
                      $this->db->order_by('rank','asc');
                      $this->db->order_by('payrollGroup','asc');
                      $data = $this->db->get('payroll_groups');

                      echo $this->htmlhelper->select_object('payrollGroupID', $data, 'payrollGroupID', 'payrollGroup', 300, $payrollGroupID);
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
        $.post("<?php echo $controller_page ?>/check_duplicate", { companyID: $('#companyID').val(), branchID: $('#branchID').val(), deptID: $('#deptID').val(), divisionID: $('#divisionID').val(), empID: $('#empID').val(), employeeTypeID: $('#employeeTypeID').val() },
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
	    	  window.location = '<?php echo site_url('employment/show') ?>';
	      }
	    });
    
});

<?php 
    echo "\n";
    $parameters = array('companyID');
    echo $this->htmlhelper->get_json_select('get_branches', $parameters, site_url('generic_ajax/get_code_branches'), 'branchID', '');
    
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


















