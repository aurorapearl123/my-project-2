          <div class="subheader">
            <div class="d-flex align-items-center">
              <div class="title mr-auto">
                <h3><i class="icon left ti-layout-column2"></i> Division</h3>
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
                        <h4 class="head-text">Add Division</h4>
                      </div>
                    </div>
                    <div class="card-head-tools"></div>
                  </div>
                  <div class="card-body">
                    <form method="post" name="frmEntry" id="frmEntry" action="<?php echo site_url("division/save") ?>">
                      <div class="table-row">
                        <table class="table-form column-3">
                          <tbody>
                            <tr>
                              <td class="form-label">Division Code <span class="asterisk">*</span></td>
                              <td class="form-group form-input">
                                <input type="text" class="form-control" name="divisionCode" id="divisionCode" required>
                              </td>
                              <td class="form-label">Company <span class="asterisk">*</span></td>
	           				  <td class="form-group form-input">
	           					<select id="companyID" name="companyID" class="form-control" data-live-search="true" liveSearchNormalize="true" required>
		                            <option value="">&nbsp;</option>
		                            <?php 
			                            $companies = $this->db->get('companies')->result();
			                            foreach($companies as $company){
		                            ?>
		                            <option value="<?php echo $company->companyID?>"><?php echo $company->companyName?></option>
		                            <?php }?>
	                            </select>
	           				  </td>
                              <td class="d-xxl-none" style="width:30%"></td>
                            </tr>
                            <tr>
                              <td class="form-label" style="width:12%">Division Name <span class="asterisk">*</span></td>
                              <td class="form-group form-input" style="width:21.33%">
                                 <input type="text" class="form-control" name="divisionName" id="divisionName" required>
                              </td>
                              <td class="form-label" style="width:10%">Branch <span class="asterisk">*</span></td>
	           				  <td class="form-group form-input">
	           					<select id="branchID" name="branchID" class="form-control" data-live-search="true" liveSearchNormalize="true" required>
		                            <option value="">&nbsp;</option>
		                            <?php 
			                            $resutls = $this->db->get('branches')->result();
			                            foreach($resutls as $res){
		                            ?>
		                            <option value="<?php echo $res->branchID?>"><?php echo $res->branchName?></option>
		                            <?php }?>
	                            </select>
	           				  </td>
                              <td class="d-xxl-none"></td>
                            </tr>
                            <tr>
	                             <td class="form-label">Division Abbr <span class="asterisk">*</span></td>
	                             <td class="form-group form-input">
	                                 <input type="text" class="form-control" name="divisionAbbr" id="divisionAbbr" required>
	                             </td>
	                             <td class="form-label">Department <span class="asterisk">*</span></td>
		           				 <td class="form-group form-input">
			           				<select id="deptID" name="deptID" class="form-control" data-live-search="true" liveSearchNormalize="true" required>
			                            <option value="">&nbsp;</option>
			                            <?php 
				                            $resutls = $this->db->get('departments')->result();
				                            foreach($resutls as $res){
			                            ?>
			                            <option value="<?php echo $res->deptID?>"><?php echo $res->deptName?></option>
			                            <?php }?>
		                            </select>
		           				 </td>
	                             <td class="d-xxl-none"></td>
                            </tr>
                            <tr>
	                              <td class="form-label">Division Contact </td>
	                              <td class="form-group form-input">
	                                 <input type="text" class="form-control" name="divisionContact" id="divisionContact" required>
	                              </td>
	                              <td class="form-label">Division Email </td>
	                              <td class="form-group form-input">
	                                 <input type="text" class="form-control" name="divisionEmail" id="divisionEmail">
	                              </td>
	                              <td class="d-xxl-none"></td>
                            </tr>
                            <tr>
	                              <td class="form-label">Division Head </td>
	                              <td class="form-group form-input">
	                                 <?php 
	                                  $this->db->where('status',1);
	                                  $heads = $this->db->get('employees')->result();
	                                  ?>
	                                <select class="form-control" id="divisionHeadID" name="divisionHeadID" data-live-search="true" livesearchnormalize="true" style="width:200px">
	                                <option value="">&nbsp;</option>
	                                <?php foreach($heads as $row) {?>
	                                    <option value="<?php echo $row->empID ?>"><?php echo $row->lname.", ".$row->fname." ".$row->mname ?></option>
	                                <?php } ?>
	                                </select>
	                              </td>
	                              <td class="form-label">Head Title <span class="asterisk">*</span></td>
	                              <td class="form-group form-input">
	                                 <input type="text" class="form-control" name="divisionHeadTitle" id="divisionHeadTitle">
	                              </td>
	                              <td class="d-xxl-none"></td>
                            </tr>
                            <tr>
	                              <td class="form-label align-text-top pt-5">Remarks </td>
	                              <td class="form-group form-input">
	                              	<textarea class="form-control" name="remarks" id="remarks"></textarea>
	                              </td>
	                              <td class="d-xxl-none" colspan="3"></td>
                            </tr>
                          </tbody>
                        </table>
                      </div>
                      <div class="form-sepator solid"></div>
                      <div class="form-group mb-0">
                        <input type="submit" name="cmdSave" id="cmdSave" value="Save" class="btn btn-primary btn-raised pill" onclick="save();"/>
                        <input type="button" id="cmdCancel" class="btn btn-outline-danger btn-raised pill" value="Cancel"/>
                      </div>
                    </form>
                  </div>
                </div>
              </div>
            </div>
          </div>
<script>
function save() 
{
	if (check_form('frmEntry')) {
		$.post("<?php echo $controller_page ?>/check_duplicate", { 
			divisionCode: $('#divisionCode').val(),
			},
		  function(data){
		    if (parseInt(data)) {
		    	// duplicate
		    	alert("Error: record is already exist!");
		    } else {
		    	// no duplicate
		    	$('#frmEntry').submit();
		    }
		  }, "text");
	}
}

$('#cmdCancel').click(function(){
    window.location = '<?php echo site_url('division/show') ?>';
});

</script>
